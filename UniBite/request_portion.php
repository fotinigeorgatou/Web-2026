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

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
$consumer_id = $_SESSION['user_id'];


if ($post_id <= 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Μη έγκυρη αγγελία."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

try{
    $checkSql = "SELECT portions_available 
                 FROM food_posts 
                 WHERE id = :post_id 
                 AND status != 'deleted'";

    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
        "post_id" => $post_id
    ]);

    $post = $checkStmt->fetch();

    if (!$post) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Η αγγελία δεν βρέθηκε."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ((int)$post['portions_available'] <= 0) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Δεν υπάρχουν διαθέσιμες μερίδες."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $duplicateSql = "SELECT id 
                     FROM requests 
                     WHERE post_id = :post_id 
                     AND consumer_id = :consumer_id 
                     AND status = 'pending'";

    $duplicateStmt = $pdo->prepare($duplicateSql);
    $duplicateStmt->execute([
        "post_id" => $post_id,
        "consumer_id" => $consumer_id
    ]);

    if ($duplicateStmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Έχετε ήδη στείλει αίτημα για αυτή την αγγελία."
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $insertSql = "INSERT INTO requests (post_id, consumer_id, status)
                  VALUES (:post_id, :consumer_id, 'pending')";

    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        "post_id" => $post_id,
        "consumer_id" => $consumer_id
    ]);

    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Το αίτημα κράτησης στάλθηκε με επιτυχία!"
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Σφάλμα βάσης: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>