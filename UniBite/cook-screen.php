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

  <title>Unibite - Οθόνη Μάγειρα</title>

  <link href="https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header>
    <h1>Unibite</h1>
    <p>Οθόνη Μάγειρα</p>

    <nav>
      <a href="home-screen.php">←Επιστροφή στην Αρχική</a>
    </nav>
  </header>

  <main>

    <section>
      <h2>Νέα Αγγελία</h2>

      <form id="food-form">

        <label for="title">Τίτλος:</label>
        <input type="text" id="title" required>

        <label for="description">Περιγραφή:</label>
        <textarea id="description" rows="4" required></textarea>

        <label for="portions">Μερίδες:</label>
        <input type="number" id="portions" min="1" required>

        <label for="location">Σημείο Παραλαβής:</label>
        <input type="text" id="location" required>

        <label for="pickup-time">Ώρα Παραλαβής:</label>
        <input type="datetime-local" id="pickup-time" required>

        <label for="allergens">Αλλεργιογόνα:</label>
        <input type="text" id="allergens" placeholder="Π.χ. Φυστίκια, Γάλα">

        <label for="photo">Φωτογραφία Φαγητού</label>
        <input type="file" id="photo" accept="image/*">

        <button type="submit" class="reserve-btn">
          Δημιουργία Αγγελίας
        </button>

      </form>
    </section>

    <section>
      <h2>Οι Αγγελίες μου</h2>

      <div id="my-posts-feed" class="food-feed"></div>
    </section>

    <section>
      <h2>Αιτήματα για τις Αγγελίες μου</h2>

      <div id="requests-feed" class="food-feed"></div>
    </section>

  </main>
  <div id="edit-modal" class="modal hidden">
    <div class="modal-content">
      <h2>Επεξεργασία Αγγελίας</h2>

      <form id="edit-form">

      <input type="hidden" id="edit-post-id">

      <label>
        Τίτλος
        <input type="text" id="edit-title" required>
      </label>

      <label>
        Περιγραφή
        <textarea id="edit-description" rows="4"></textarea>
      </label>

      <label>
        Σύνολο Μερίδων
        <input type="number" id="edit-portions-total" min="1" required>
      </label>

      <label>
        Διαθέσιμες Μερίδες
        <input type="number" id="edit-portions-available" min="0" required>
      </label>

      <label>
        Σημείο Παραλαβής
        <input type="text" id="edit-location" required>
      </label>

      <label>
        Ώρα Παραλαβής
        <input type="datetime-local" id="edit-pickup-time" required>
      </label>

      <button type="submit" class="reserve-btn">Αποθήκευση Αλλαγών</button>
      <button type="button" id="close-edit-modal" class="reserve-btn">Ακύρωση</button>

    </form>
  </div>
</div>
<script src="cook-screen.js"></script>

</body>

</html>