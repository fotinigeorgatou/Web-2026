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

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
$consumer_id = $_SESSION['user_id'];
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : null; //1 ως 5

if (!$request_id || !$consumer_id || !$post_id ||!$rating || $rating < 1 || $rating > 5){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Μη έγκυρα ή ελλιπή δεδομένα αξιολόγησης."], JSON_UNESCAPED_UNICODE);
    exit();
}

try{
    $pdo->beginTransaction();
    
    //Αποθήκευση της αξιολόγησης στον πίνακα rating
    $sql_rate = "INSERT INTO ratings (request_id, consumer_id, post_id, rating, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt_rate = $pdo->prepare($sql_rate);
    $stmt_rate->execute([$request_id, $consumer_id, $post_id, $rating]);
    
    //Εύρεση του  μάγειρα από τον πίνακα food_posts
    $sql_cook = "SELECT cook_id FROM food_posts WHERE id = ?";
    $stmt_cook = $pdo->prepare($sql_cook);
    $stmt_cook->execute([$post_id]);
    $post = $stmt_cook->fetch(PDO::FETCH_ASSOC);
    
    if(!$post){
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Δεν βρέθηκε η αγγελία."], JSON_UNESCAPED_UNICODE);
    $pdo->rollBack();
    exit();
}

$cook_id = $post['cook_id'];

//Αν το rating >3 παίρνει 3 πόντους, αλλιώς 2
$points_to_add = ($rating > 3) ? 2 : 1;

//Ενημέρωση πόντων
$sql_update_cook = "UPDATE users SET credits = credits + ? WHERE id = ?";
$stmt_update_cook = $pdo->prepare($sql_update_cook);
$stmt_update_cook->execute([$points_to_add, $cook_id]);
if ($_SESSION['user_id'] == $cook_id) {
    $_SESSION['credits'] += $points_to_add;
}

//Καταγραφή της συναλλαγής για το ιστορικό στο credits_transaction
$reason = "Επιβράβευση από αξιολόγηση (" . $rating . "/5 αστέρια)";
$sql_trans = "INSERT INTO credit_transactions (user_id, amount, reason, related_request_id) VALUES (?, ?, ?, ?)";
$stmt_trans = $pdo->prepare($sql_trans);
$stmt_trans->execute([$cook_id, $points_to_add, $reason, $request_id]);

$pdo->commit();
http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Η αξιολόγηση υποβλήθηκε! Ο μάγειρας έλαβε + " . $points_to_add . " πόντους"
], JSON_UNESCAPED_UNICODE);

}catch (PDOException $e){
    $pdo->rollBack();
    http_response_code(500);
     echo json_encode(["status" => "error", "message" => "Σφάλμα βάσης: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
