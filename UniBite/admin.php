<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
   header("Location: http://localhost/UniBite/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"> 
        <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="admin.css">
    </head>
    <body>
        <div class="admin-menu">
            <header>
                <h1>Unibite</h1>
                <p>Φοιτητικό Food Sharing</p>
                <p>
                    Καλώς ήρθες, <?php echo $_SESSION['username']; ?> 
                </p>
                <ul>
                    <li><a href="statistics.php">Statistics</a></li>
                    <li><a href="leaderboard.php">Leaderboard</a></li>
                    <li><a href="new_admin.php">Add admin</a></li>
                    <li style="float:right"><a href="logout.php">Log out</a></li>
                </ul>
            </header>
        </div>  
    </body>
</html>