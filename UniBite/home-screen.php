<?php
session_start();
include 'unibite_db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

try{
    $sql = "SELECT title, portions_available, pickup_lat, pickup_lng FROM food_posts";
    $result = $pdo->query($sql);

    $ads_from_db = [];

    if ($result) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $ads_from_db[] = [
                'title' => $row['title'],
                'portions' => (int)$row['portions_available'],
                'lat' => (float)$row['pickup_lat'],
                'lng' => (float)$row['pickup_lng']
            ];
        }
    }
}catch (PDOException $e) {
    die("Σφάλμα βάσης δεδομένων: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Unibite</title>

    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="map.css">
</head>

<body>

    <header>
        <h1>Unibite</h1>
        <p>Φοιτητικό Food Sharing</p>
        <p>
          Καλώς ήρθες, <?php echo $_SESSION['username']; ?> |
          Credits: <?php echo $_SESSION['credits']; ?>
        </p>
        <nav>
            <a href="cook-screen.php">Μετάβαση στην Οθόνη Μάγειρα</a>
            <a href="consumer-screen.php">Μετάβαση στην Οθόνη Καταναλωτή</a>
            <a href="logout.php" class="logout-btn">Αποσύνδεση</a>
        </nav>
    </header>

    <main>
        
        <section>
            <h2>Διαθέσιμα Φαγητά</h2>

            <div id="food-feed" class="food-feed">

            </div>
        </section>
        <section>

             <h2>UniMap</h2>
            <form class="search_engine">
                <div class="search-container">
                    <h3>Φιλτράρισμα Αγγελιών</h3>
                    <label>Εισάγετε Διεύθυνση:</label>
                    <input type="text" id="search-address" placeholder="π.χ. Πανεπιστημίου 25, Πάτρα" required>
                    <button type="submit" class="search-btn"> Αναζήτηση</button>
                </div>
            </form>
             <div id="map" style="height: 500px;" data-ads="<?php echo htmlspecialchars(json_encode($ads_from_db), ENT_QUOTES, 'UTF-8'); ?>"></div>

            
        </section>
        
    </main>

    <script>
      const loggedInUserId = <?php echo $_SESSION['user_id']; ?>;
    </script>

     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
     <script src="home-screen.js"></script>
     <script src="map.js"></script>
</body>

</html>