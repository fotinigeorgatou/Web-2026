<?php
header("Content-Type: application/json; charset=UTF-8");

include 'unibite_db.php';

try {
     //Παίρνουμε τις αγγελίες που είναι εντός 48ωρου και
     //δεν έχουν διαγραφεί χειροκίνητα
    $sql = "SELECT 
                id,
                cook_id,
                title,
                description,
                photo,
                portions_total,
                portions_available,
                pickup_location,
                pickup_time,
                created_at,
                allergens,
                status
            FROM food_posts
            WHERE status != 'deleted'
            AND created_at >= NOW() - INTERVAL 48 HOUR
            ORDER BY created_at DESC";

    $stmt = $pdo->query($sql);
    $ads = $stmt->fetchAll();

    foreach ($ads as &$ad) {
        if ((int)$ad['portions_available'] <= 0) {
            $ad['status'] = 'inactive';
        } else {
            $ad['status'] = 'active';
        }

        if (!empty($ad['allergens'])) {
            $ad['allergens'] = array_map('trim', explode(',', $ad['allergens']));
        } else {
            $ad['allergens'] = [];
        }
    }

    echo json_encode([
        "status" => "success",
        "data" => $ads
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => "Σφάλμα κατά την ανάκτηση: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>