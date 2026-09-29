<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'unibite_db.php';
include 'admin.php';

//top donor
$donor_id = 0;
$donor_name = "-";
$donor_email= "-";
$total_portions = 0;

$sql_donor= "SELECT u.id AS user_id, u.name AS donor_name, u.email AS donor_email, (SUM(fp.portions_total)-SUM(fp.portions_available)) AS total_portions_donated FROM users u JOIN food_posts fp ON u.id = fp.cook_id WHERE fp.created_at>= NOW()- INTERVAL 30 DAY GROUP BY u.id, u.name, u.email ORDER BY total_portions_donated DESC LIMIT 1";
$stmt_donor=$pdo->query($sql_donor);


if ($stmt_donor && $top_donor = $stmt_donor->fetch(PDO::FETCH_ASSOC)) {
    $donor_id = $top_donor['user_id'];
    $donor_name = $top_donor['donor_name'];
    $donor_email= $top_donor['donor_email'];
    $total_portions = $top_donor['total_portions_donated'];
    
} else {
    echo "Δεν βρέθηκαν δεδομένα.";
}
//statistics for top 5 meals
$sql_meals="SELECT fp.id AS post_id,fp.title AS meal_title, fp.cook_id AS cook_id, AVG(r.rating) AS average_rating, COUNT(r.id) AS total_ratings_count FROM food_posts fp JOIN ratings r ON fp.id = r.post_id WHERE r.created_at>= NOW()- INTERVAL 30 DAY GROUP BY fp.id, fp.title ORDER BY average_rating DESC, total_ratings_count DESC LIMIT 5";
$stmt_meals=$pdo->prepare($sql_meals);
$stmt_meals->execute();

$top_meals=[];

while($row= $stmt_meals->fetch(PDO::FETCH_ASSOC)){
    $top_meals[]=[
        'post_id' => $row['post_id'],
        'meal_title' => $row['meal_title'],
        'cook_id'=>$row['cook_id'],
        'average_rating' => round($row['average_rating'], 1), // Στρογγυλοποίηση βαθμολογίας
        'total_ratings_count' => $row['total_ratings_count']
    ];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"> 
        <link rel="stylesheet" href="leaderboard.css">
    </head>
    <body>
        <div class=top-donor>
            <h2>Top donor for this month:</h2>
            <img src="images/top-donor.jpeg" alt="profile-icon" style="width:100px;height:100px;">
            <h1><?php echo htmlspecialchars($donor_name); ?></h1>
            <ul style="list-style-type: square;">
                <li>User Id: <?php echo intval($donor_id); ?></li>
                <li>User Webmail: <?php echo htmlspecialchars($donor_email); ?></li>
            </ul>
            <p>Total Donated Portions:
                <?php echo number_format($total_portions); ?>
            </p>
        </div>
        <div class="meals_statistics">
            <h2>Top 5 Meals of the Month:</h2>
            <?php
                function createLeaderboardList($arr) {
                    if (empty($arr)) {
                        return '<p>Δεν βρέθηκαν δεδομένα για το leaderboard.</p>';
                    }
                    $html = '<ul style="list-style-type: decimal";>';
                     foreach ($arr as $item) {
                        // Ασφαλής ανάκτηση των στοιχείων με Null Coalescing Operator
                        $title = $item['meal_title'] ?? '-';
                        $id = $item['post_id'] ?? '-';
                        $cook_id=$item['cook_id'] ??'-';
                        $rating = $item['average_rating'] ?? '0';
                        $count = $item['total_ratings_count'] ?? '0';
                        // Κύριο bullet: Τίτλος Φαγητού
                        $html .= '<li>';
                        $html .= '<strong>' . htmlspecialchars($title) . '</strong>';
        
                        // Υπο-λίστα με τα υπόλοιπα στοιχεία
                        $html .= '<ul style="list-style-type: square;">';
                        $html .= '<li>ID Γεύματος: ' . htmlspecialchars($id) . '</li>';
                        $html .= '<li>ID Μάγειρα: ' . htmlspecialchars($cook_id) . '</li>';
                        $html .= '<li>Μέση Βαθμολογία: ' . htmlspecialchars($rating) . '/5 </li>';
                        $html .= '<li>Συνολικές Κριτικές: ' . htmlspecialchars($count) . '</li>';
                        $html .= '</ul>';

                        $html .= '</li>'; // Κλείσιμο κύριου bullet
                        
                    }
                    $html .= '</ul>';
                    return $html;
                }
                echo createLeaderboardList($top_meals);
            ?>

        </div>
    </body>
</html>