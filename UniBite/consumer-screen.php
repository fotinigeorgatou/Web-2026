<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Unibite - Οθόνη Καταναλωτή</title>

  <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header>
    <h1>Unibite</h1>
    <p>Οθόνη Καταναλωτή</p>

    <nav>
      <a href="home-screen.php">← Επιστροφή στην Αρχική</a>
    </nav>
  </header>

  <main>
    <section>
      <h2>Γεύματα προς Αξιολόγηση</h2>

      <div id="rating-feed" class="food-feed"></div>
    </section>
  </main>

  <script src="consumer-screen.js"></script>

</body>

</html>