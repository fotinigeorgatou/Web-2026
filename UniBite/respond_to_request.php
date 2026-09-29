<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'unibite_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
$action = isset($_POST['action']) ? $_POST['action'] : ''; //Πρέπει να είναι approved ή rejected


if(!$request_id || !in_array($action, ['approved', 'rejected'])){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Μη έγκυρα δεδομένα ή ενέργεια."], JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    $pdo->beginTransaction();
    
    //Εύρεση του post_id από το request
    $sql_req = "SELECT post_id, status FROM requests WHERE id = ?";
    $stmt_req = $pdo->prepare($sql_req);
    $stmt_req->execute([$request_id]);
    $request = $stmt_req->fetch(PDO::FETCH_ASSOC);
    
    if(!$request || $request['status'] !== 'pending'){
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Το αίτημα δεν εκκρεμεί ή δεν υπάρχει"], JSON_UNESCAPED_UNICODE);
        $pdo->rollBack();
        exit();
    }
    
    $post_id = $request['post_id'];
    
    //Ενημέρωση της κατάστασης του αιτήματος
    $sql_update = "UPDATE requests SET status = ?, approved_at = IF(? = 'approved', NOW(), NULL) WHERE id = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$action, $action, $request_id]);
    
    //Αν γίνει approve, μειώνουμε τις διαθέσιμες μερίδες κατά 1
    if($action == 'approved'){
        $sql_minus = "UPDATE food_posts SET portions_available = portions_available - 1 WHERE id = ? AND portions_available > 0";
        $stmt_minus = $pdo->prepare($sql_minus);
        $stmt_minus->execute([$post_id]);
    }
    
    $pdo->commit();
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Το αίτημα ενημερώθηκε σε: " . $action], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e){
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
