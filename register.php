<?php
// register.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registrera användare</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Registrera användare</h1>
  <form action="process_register.php" method="post">
  <label>Användarnamn:</label>
  <input type="text" name="username" required>

  <label>Lösenord:</label>
  <input type="password" name="password" required>

  <label>Plats:</label>
  <select name="location_id" required>
    <option value="">Välj plats</option>
    <?php
    include 'db.php';
    $stmt = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
    while ($row = $stmt->fetch()) {
        echo "<option value='{$row['id']}'>{$row['location_name']}</option>";
    }
    ?>
  </select>

  <button type="submit">Registrera</button>
</form>

  <p>Har du redan ett konto? <a href="login.php">Logga in här</a>.</p>
</body>
</html>
