<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'unibite_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;

if(!$post_id){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Λείπει το post_id."], JSON_UNESCAPED_UNICODE);
    exit();
}

try{
    //Αλλάζουμε το status σε deleted ώστε να μην εμφανίζεται πουθενά
    $sql ="UPDATE food_posts SET status = 'deleted' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    
    http_response_code(200);
    echo json_encode (["status" => "success", "message" => "Η αγγελία διαγράφηκε χειροκίνητα."], JSON_UNESCAPED_UNICODE);
}catch (PDOException $e){
    http_response_code(500);
    echo json_encode(["status" => "error", "message" =>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
