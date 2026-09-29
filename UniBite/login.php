<?php
    session_start();
    include 'unibite_db.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        "email" => $email
    ]);

    $user = $stmt->fetch();

    if ($user) {
        //Επαληθευση κωδικου
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['credits'] = $user['credits'];

            if ($user['role'] == 'admin') {
              //Ανακατευθυνση στη σελιδα του αντμιν
                header("Location: statistics.php");
                exit();
            } else {
                header("Location: home-screen.php");
                exit();
            }
        } else {
            $error = "Λάθος κωδικός πρόσβασης.";
        }
    } else {
        $error = "Ο λογαριασμός δεν βρέθηκε.";
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>UniBite - Σύνδεση</title>

  <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">    
</head>

<body>

  <main class="auth-page">

    <form class="auth-form" method="POST">

      <h1 class="auth-logo">UniBite</h1>

      <label>
        Webmail:
        <input type="email" name="email" placeholder="Enter webmail" required>
      </label>

      <label>
        Password:
        <input type="password" name="password" placeholder="Enter Password" required>
      </label>

      <?php if (isset($error)) { ?>
      <p class="error-message"><?php echo $error; ?></p>
      <?php } ?>
      
      <button type="submit" class="auth-btn">Σύνδεση</button>

      <p class="auth-link">
        New here? Create an account
        <a href="signup.php">SignUp</a>
      </p>

    </form>

  </main>

</body>
</html>




