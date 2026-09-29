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

$consumer_id = $_SESSION['user_id'];

try {
    $sql = "SELECT 
                r.id AS request_id,
                r.post_id,
                f.title AS food_title,
                f.description,
                f.pickup_time,
                u.name AS cook_name
            FROM requests r
            JOIN food_posts f ON r.post_id = f.id
            JOIN users u ON f.cook_id = u.id
            LEFT JOIN pickups p ON r.id = p.request_id
            LEFT JOIN ratings rt ON r.id = rt.request_id
            WHERE r.consumer_id = :consumer_id
              AND r.status = 'approved'
              AND p.status = 'picked_up'
              AND rt.id IS NULL
            ORDER BY p.picked_up_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        "consumer_id" => $consumer_id
    ]);

    $meals = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data" => $meals
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Σφάλμα βάσης: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>