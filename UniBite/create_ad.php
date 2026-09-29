<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'unibite_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"], JSON_UNESCAPED_UNICODE);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Δεν είστε συνδεδεμένος χρήστης."
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$cook_id = $_SESSION['user_id'];

//Λήψη δεδομένων από το POST
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$portions_total = isset($_POST['portions_total']) ? intval($_POST['portions_total']) : 0;
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
//Συνταταγμένες και φώτο
$coordinates = getCoordinates($pickup_location);
$pickup_lat = $coordinates['lat'];
$pickup_lng = $coordinates['lng'];
$photo = null;

if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] === UPLOAD_ERR_OK
) {

    $uploadDir = "images/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['photo']['name']);

    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photo = $targetFile;
    }
}

//Λίστα με τα ID αλλεργιογόνων an stalo;yn
$allergens = isset($_POST['allergens']) ? trim($_POST['allergens']) : '';

if(empty($title) || $portions_total <= 0){
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία ."], JSON_UNESCAPED_UNICODE);
    exit();
} 

try{
    $pdo->beginTransaction();
    
    //Εισαγωγή στον πίνακα food_posts
    $sql = "INSERT INTO food_posts(cook_id, title, description, photo, portions_total, portions_available, pickup_location, pickup_lat, pickup_lng, pickup_time, allergens, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cook_id, $title, $description, $photo, $portions_total, $portions_total, $pickup_location, $pickup_lat, $pickup_lng, $pickup_time, $allergens]);
    
    $post_id = $pdo->lastInsertId();
    
    //Αν υπάρχουν αλλεργιογόνα τα συνδέουμε στον πίνακα post_allergens
    if(!empty($allergen_ids) && is_array($allergen_ids)){
        $sql_allergen = "INSERT INTO post_allergens (post_id, allergen_id) VALUES (?, ?)";
        $stmt_allergen = $pdo->prepare($sql_allergen);
        foreach($allergen_ids as $all_id){
            $stmt_allergen->execute([$post_id, intval($all_id)]);
        }
    }

$pdo->commit();
http_response_code(201);
echo json_encode(["status" => "success", "message" => "Η αγγελία δημιουργήθηκε με επιτυχία!", "post_id" => $post_id], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e){
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Σφάλμα βάσης: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>