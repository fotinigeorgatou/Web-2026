<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'unibite_db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Δεν είστε συνδεδεμένος χρήστης."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$cook_id = $_SESSION['user_id'];

//Παίρνουμε το id του μέαγειρα, έστω 1 για δοκιμή
//$cook_id = isset($_GET['cook_id']) ? intval($_GET['cook_id']) : 1;


try{
    //Κάνουμε JOIN για να φέρουμε τίτλο φαγητού και όνομα φοιτητή που το ζητάει
   $sql = "SELECT
            r.id AS request_id,
            r.post_id,
            f.title AS food_title,
            u.name AS consumer_name,
            r.status,
            r.requested_at
        FROM requests r
        JOIN food_posts f ON r.post_id = f.id
        JOIN users u ON r.consumer_id = u.id
        LEFT JOIN pickups p ON r.id = p.request_id
        WHERE f.cook_id = ?
          AND p.id IS NULL
        ORDER BY r.requested_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cook_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "count" => count($requests),
        "data" => $requests
    ], JSON_UNESCAPED_UNICODE);        
} catch (PDOException $e){
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
