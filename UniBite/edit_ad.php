<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'unibite_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

//Λήψη δεδομένων από το POST
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$portions_total = isset($_POST['portions_total']) ? intval($_POST['portions_total']) : 0;
$portions_available = isset($_POST['portions_available']) ? intval($_POST['portions_available']) : 0;
$pickup_location = isset($_POST['pickup_location']) ? $_POST['pickup_location'] : '';
$pickup_time = isset($_POST['pickup_time']) ? $_POST['pickup_time'] : '';

//Συντεταγμένες με χρήση nominatim
function getCoordinates($pickup_location){
    $encodedLocation=urlencode($pickup_location);
    $url = "https://nominatim.openstreetmap.org/search?q={$encodedLocation}&format=json&limit=1";
    //Το Nominatim απαιτεί υποχρεωτικά ένα User-Agent Header για να ξέρει ποιος κάνει το αίτημα
    $options = [
        "http" => [
            "header" => "User-Agent: StudentApp/1.0 (up1108358@ac.upatras.gr)\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        return null; // Αποτυχία σύνδεσης με το API
    }

    // Αποκωδικοποίηση του JSON αποτελέσματος
    $data = json_decode($response, true);

    // Αν βρέθηκαν αποτελέσματα, επιστρέφουμε το πρώτο (πιο ακριβές)
    if (!empty($data)) {
        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon']
        ];
    }
    return null; // Δεν βρέθηκε η διεύθυνση
}

$coordinates = getCoordinates($pickup_location);
$pickup_lat = $coordinates['lat'];
$pickup_lng = $coordinates['lng'];

if(!$post_id || empty($title)){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Μη έγκυρα δεδομένα."], JSON_UNESCAPED_UNICODE);
    exit();
}

try{
    $sql = "UPDATE food_posts SET
               title = ?,
               description = ?,
               portions_total = ?,
               portions_available = ?,
               pickup_location = ?,
               pickup_time = ?,
               pickup_lat=?,
               pickup_lng=?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $portions_total, $portions_available, $pickup_location, $pickup_time,$pickup_lat,$pickup_lng, $post_id]);
    
    http_response_code(200);
    echo json_encode(["status" => "success", "message" => "Η αγγελία ενημερώθηκε επιτυχώς:"], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e){
    http_response_code(500);
    echo json_encode(["status" => "error", "message" =>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>