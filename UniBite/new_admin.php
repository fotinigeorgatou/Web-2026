<?php
session_start();
include 'unibite_db.php';
include 'admin.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $webmail = $_POST['webmail'];
    $plainPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($plainPassword !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($plainPassword) < 8) {
        $error = "Password must have at least 8 characters";
    } else {
        $password = password_hash($plainPassword, PASSWORD_DEFAULT);

        $check_username_query = "SELECT * FROM users WHERE name = :name";
        $stmt = $pdo->prepare($check_username_query);
        $stmt->execute([
            "name" => $name
        ]);

        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "The username is already taken. Choose another one.";
        } else {
            $insert_query = "INSERT INTO users (name, email, password,role) VALUES (:name, :email, :password,:role)";

            $stmt = $pdo->prepare($insert_query);
            $stmt->execute([
                "name" => $name,
                "email" => $webmail,
                "password" => $password,
                "role"=> "admin"
            ]);
            header("Location: login.php");
            exit();
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="el">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>UniBite - Εγγραφή</title>

  <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="new_admin.css">
</head>

<body>

  <main class="auth-page">

    <form id="form" class="auth-form" method="POST">

      <h1 class="auth-logo">UniBite</h1>
      <h2 class="auth-title">New admin</h2>

      <label>
        Username:
        <input type="text" id="name" name="name" placeholder="Enter Username" required>
      </label>

      <label>
        Webmail:
        <input type="email" id="webmail" name="webmail" placeholder="Enter webmail" required>
      </label>

      <label>
        Password:
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
      </label>

      <label>
        Re-enter Password:
        <input type="password" id="repeat-password" name="confirm_password" placeholder="Enter Password" required>
      </label>

      <p id="errors-message"></p>

      <button type="submit" class="auth-btn">Εγγραφή</button>
      </p>

    </form>

  </main>
  <script src="validation.js"></script>
</body>
</html>