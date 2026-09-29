<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'unibite_db.php';
include 'admin.php';

try{
    $sql="SELECT SUM(portions_total-portions_available)AS total FROM food_posts WHERE created_at>=NOW()-INTERVAL 30 DAY";

    $result = $pdo->query($sql);
    $shared_portions = 0;
    if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {
    
        $shared_portions = $row['total'] ?? 0; 
    } 

    //chart
    $sql_chart = "SELECT SUM(portions_total - portions_available) AS weekly_total FROM food_posts WHERE created_at >= NOW() - INTERVAL 30 DAY GROUP BY WEEK(created_at) LIMIT 4";
    $results_chart = $pdo->query($sql_chart);

    $weekly_results = [
        ["label" => "Εβδομάδα 1", "y" => 0],
        ["label" => "Εβδομάδα 2", "y" => 0],
        ["label" => "Εβδομάδα 3", "y" => 0],
        ["label" => "Εβδομάδα 4", "y" => 0]
    ];

    $i = 0;
    while ($row = $results_chart->fetch(PDO::FETCH_ASSOC)) {
        if ($i < 4) {
            $weekly_results[$i]["y"] = (int)$row['weekly_total'];
            $i++;
        }
    }
}catch (PDOException $e) {
    die("Σφάλμα βάσης δεδομένων: " . $e->getMessage());
}
$pdo=null;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"> 
        <link rel="stylesheet" href="statistics.css" type="text/css">
        <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
        
    </head>
    <body>
        <h1>Statistics</h1>
        <div class="kpi-container">
            <p class="kpi-text">
                <span class="kpi-number"><?php echo number_format($shared_portions); ?></span> 
                <span class="kpi-unit">επιτυχείς διαμοιρασμοί (Τελευταίες 30 ημέρες)</span>
            </p>
        </div>
        <div id="chartContainer" style="height: 370px; width: 100%; max-width: 600px; margin: auto; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 0 auto 50px auto;"></div>
        <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1", 
                title:{
                    text: "Monthly Meals Chart",
                },
                axisY:{
                    includeZero: true,
                    title: "Μερίδες"
                },
                data: [{
                    type: "column", 
                    indexLabel: "{y}", 
                    indexLabelFontColor: "#5A5757",
                    indexLabelPlacement: "outside",   
                    // Περνάμε τον έτοιμο JSON πίνακα αντικειμένων
                    dataPoints: <?php echo json_encode($weekly_results, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        }
        </script>
    </body>
</html>