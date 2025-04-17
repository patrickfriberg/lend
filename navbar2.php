
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.png">
</head>

<body>
  <nav class="navbar">
  <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <ul id="menu">
      <li><a href="index.php">Hem</a></li>
      <li><a href="products_by_category.php">Låna ut</a></li>
      <li><a href="search_product.php">Söka</a></li>
      <li><a href="utlanade.php">Utlånat</a></li>
      <li><a href="create_product.php">Skapa produkt</a></li>
      <li><a href="loan_statistics.php">Statistik</a></li>
      <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
  <li><a href="admin.php">Adminpanel</a></li>
<?php endif; ?>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="logout.php">Logga ut</a></li>
      <?php else: ?>
        <li><a href="login.php">Logga in</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <script>
function toggleMenu() {
  document.getElementById("menu").classList.toggle("show");
}
</script>

</body>
</html>
