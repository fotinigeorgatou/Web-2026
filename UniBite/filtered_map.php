<?php 
header('Content-Type: application/json');
include 'unibite_db.php';

function getCoordinates($location){
    $encodedLocation = urlencode($location);
    $url = "https://nominatim.openstreetmap.org/search?q={$encodedLocation}&format=json&limit=1";
    
    $options = [
        "http" => [
            "header" => "User-Agent: StudentApp/1.0 (up1108358@ac.upatras.gr)\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);

    if (!empty($data)) {
        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon']
        ];
    }
    return null;
}

if (isset($_POST['address']) && !empty($_POST['address'])) {
    $address = $_POST['address'];
    $coordinates = getCoordinates($address);
    
    if ($coordinates === null) {
        echo json_encode(['error' => 'Η διεύθυνση δεν βρέθηκε.']);
        exit;
    }

    $user_lat = $coordinates['lat'];
    $user_lng = $coordinates['lng'];
    $radius = 5.0; 
    try{
        //select τις παραγγελίες που βρίσκονται σε ακτίνα 5 χλμ από την διεύθνση που θέλει ο χρήστης
        $sql = "SELECT id, cook_id, title, description, portions_available, pickup_lat, pickup_lng, pickup_location, pickup_time, allergens, photo,
            (SQRT(POW(pickup_lat - ?, 2) + POW(pickup_lng - ?, 2)) * 111) AS distance 
            FROM food_posts HAVING distance < ? ORDER BY distance ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_lat, $user_lng, $radius]);


        $nearby_ads = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nearby_ads[] = [
                'id'=>(int)$row['id'],
                'cookid'=>(int)$row['cookid'],
                'title' => $row['title'],
                'portions' => (int)$row['portions_available'],
                'lat' => (float)$row['pickup_lat'],
                'lng' => (float)$row['pickup_lng'],
                'pickup_location'=>$row['pickup_location']
            ];
        }

        echo json_encode([
            'userLat' => $user_lat,
            'userLng' => $user_lng,
            'ads' => $nearby_ads
        ]);
    }catch (PDOException $e) {
        echo json_encode(['error' => 'Σφάλμα βάσης: ' . $e->getMessage()]);
    } 

} else {
    echo json_encode(['error' => 'Δεν δόθηκε διεύθυνση.']);
} 
?>

