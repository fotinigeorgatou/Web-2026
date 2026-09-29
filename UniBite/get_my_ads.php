<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

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

try {
    $sql = "SELECT
                id,
                title,
                description,
                portions_total,
                portions_available,
                pickup_location,
                pickup_time,
                allergens,
                status,
                created_at
            FROM food_posts
            WHERE cook_id = :cook_id
              AND status != 'deleted'
            ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        "cook_id" => $cook_id
    ]);

    $posts = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data" => $posts
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Σφάλμα βάσης: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>