<?php
$host = "127.0.0.1";
$username = "root";      // Το προεπιλεγμένο όνομα χρήστη στο XAMPP
$password = "ceid.upatras1";          // Ο προεπιλεγμένος κωδικός στο XAMPP είναι κενός
$dbname = "unibite_db";     

// Δημιουργία σύνδεσης
try{
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Η σύνδεση απέτυχε: " . $e->getMessage());
}
?>

