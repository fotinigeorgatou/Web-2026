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
$appeared = isset($_POST['appeared']) ? $_POST['appeared'] : ''; //yes/no

if(!$request_id || !in_array($appeared, ['yes', 'no'])){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Μη έγκυρα δεδομένα."], JSON_UNESCAPED_UNICODE);
    exit();
}

try{
    $pdo->beginTransaction();
    
    //Βρίσκουμε τον καταναλωτή
    $sql_find = "SELECT consumer_id FROM requests WHERE id = ?";
    $stmt_find = $pdo->prepare($sql_find);
    $stmt_find->execute([$request_id]);
    $request = $stmt_find->fetch(PDO::FETCH_ASSOC);
    
    if(!$request){
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Δεν βρέθηκε το αίτημα."], JSON_UNESCAPED_UNICODE);
    exit();
}

$consumer_id = $request['consumer_id'];
$pickup_status = ($appeared === 'yes') ? 'picked_up' : 'not picked_up';

//Εισαγωγή στον πίνακα pickups
$sql_pick = "INSERT INTO pickups (request_id, status, picked_up_at) VALUES (?, ?, NOW())";
$stmt_pick = $pdo->prepare($sql_pick);
$stmt_pick->execute([$request_id, $pickup_status]);

//Αν δεν εμφανίστηκε αφαιρούμε έναν πόντο
if($appeared === 'no'){
    $sql_penalty = "UPDATE users SET credits = credits - 1 WHERE id = ?";
    $stmt_penalty = $pdo->prepare($sql_penalty);
    $stmt_penalty->execute([$consumer_id]);
    
    //Καταγραφή ποινής
    $sql_trans = "INSERT INTO credit_transactions (user_id, amount, reason, related_request_id) VALUES (?, ?, ?, ?)";
    $stmt_trans = $pdo->prepare($sql_trans);
    $stmt_trans->execute([$consumer_id, -1, 'Ποινή μη εμφάνισης', $request_id]);
}

$pdo->commit();
http_response_code(200);
echo json_encode(["status" => "success", "message" => "Η καταγραφή παραλαβής ολοκληρώθηκε!"], JSON_UNESCAPED_UNICODE);   

} catch (PDOException $e){
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
