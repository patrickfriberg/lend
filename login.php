<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FABRIKEN - Inloggning</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.png">
</head>
<body>
  <h1>Logga in</h1>
  <?php
    if(isset($_SESSION['error'])) {
        echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
  ?>
  <form action="process_login.php" method="post">
    <label>Användarnamn:</label>
    <input type="text" name="username" required>
    
    <label>Lösenord</label>
    <input type="password" name="password" required>
    
    <button type="submit" class="returnlink">Logga in</button>
  </form>
  <?php if (isset($_GET['nonapproved'])): ?>
  <p style="color: red; font-weight: bold;">🔒 Ditt konto väntar på godkännande från administratören.</p>
<?php endif; ?>
  <p>Saknar du konto? <a href="register.php">Registrera dig</a>.</p>

  
</body>
</html>
