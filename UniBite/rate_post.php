<?php
//1. Εμφάνιση σφαλμάτων
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Ορίζουμε ζώνη ώρας για να παίρνουν οι αγγελίες στην sql την σωστή ώρα
date_default_timezone_set('Europe/Athens');

//2. Σύνδεση με βάση
include 'db.php';

$message = "";

// Για τη δοκιμή, ας υποθέσουμε ότι βαθμολογούμε την αγγελία με ID = 1
// και ο συνδεδεμένος χρήστης (consumer) είναι ο χρήστης με ID = 2
$post_id_to_rate = 1; 
$current_user_id = 2; 

// Δημιουργία εικονικού καταναλωτή (αν δεν υπάρχει) για να δουλέψει η δοκιμή
try {
    $checkUser = $pdo->query("SELECT id FROM users WHERE id = 2");
    if(!$checkUser->fetch()){
        $pdo->query("INSERT INTO users(id, name, email, password, role, credits) VALUES (2, 'Μαρία', 'maria@mail.com', '123456', 'student', 5)");
    }
} catch (PDOException $e) { }

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $stars = intval($_POST['stars']);
    $comment = $_POST['comment'];
    
    try{
        //Ξεκινάμε transaction για να γίνονται όλες οι αλλαγές με ασφάλεια
        $pdo->beginTransaction();
        
        //Βρίσκουμε τον μάγειρα της αγγελίας
        $sql_cook = "SELECT cook_id FROM food_posts WHERE id =?";
        $stmt_cook = $pdo->prepare($sql_cook);
        $stmt_cook->execute([$post_id_to_rate]);
        $food = $stmt_cook->fetch();
        
        if ($food){
            $cook_id = $food['cook_id'];
            
            //Buisness Logic: Αν η βαθμολογία έιναι πάνω από 3 δώσε +2 πόντους στον μάγειρα
            if($stars > 3){
                $sql_reward = "UPDATE users SET credits = credits + 2 WHERE id = ?";
                $stmt_reward = $pdo->prepare($sql_reward);
                $stmt_reward->execute([$cook_id]);
                $message = "<p style='color:green; font-weight:bold;'>Η αξιολόγηση καταχωρήθηκε! Ο μάγειρας πήρε +2 πόντους</p>";        
            } else {
                $message = "<p style='color:orange; font-weight:bold;'>Η αξιολόγηση καταχωρήθηκε!</p>";        
            } 
        }else{
            $message = "<p style='color:red;'>Δεν βρέθηκε αγγελία!</p>";
           
        }
        
        $pdo->commit();
    } catch (Exception $e){
        $pdo->rollBack();
        $message = "<p style='color:red'> Σφάλμα: " . $e->getMessage() . "</p>";
    }          
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Αξιολόγηση Φαγητού - UniBite</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f4; }
        .rate-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); max-width: 400px; }
        select, textarea, button { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; border: none; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="rate-container">
    <h2>Αξιολόγηση Φαγητού</h2>
    <?php echo $message; ?>

    <form action="rate_post.php" method="POST">
        <label>Πόσο σου άρεσε το φαγητό;</label>
        <select name="stars" required>
            <option value="5">5 Αστέρια (Εξαιρετικό)</option>
            <option value="4">4 Αστέρια (Πολύ καλό)</option>
            <option value="3">3 Αστέρια (Μέτριο)</option>
            <option value="2">2 Αστέρια (Κακό)</option>
            <option value="1">1 Αστέρι (Απαράδεκτο)</option>
        </select>

        <label>Σχόλια (Προαιρετικά):</label>
        <textarea name="comment" rows="3" placeholder="π.χ. Ήταν πεντανόστιμο!"></textarea>

        <button type="submit">Υποβολή Αξιολόγησης</button>
    </form>
    <br>
    <a href="index.php"><- Επιστροφή στην Αρχική</a>
</div>

</body>
</html>