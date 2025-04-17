<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
  die("Produkt ej hittad.");
}
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Låna ut Produkt</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Låna ut Produkt: <?= htmlspecialchars($product['typ']) ?> (ID: <?= $product['id'] ?>)</h1>
  
  <form action="process_lend.php" method="post">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <input type="hidden" name="location_id" value="<?= $product['location_id'] ?>">
    
    <label>Namn:</label>
    <input type="text" name="lånant_namn" required>
    
    <label>Telefonnummer:</label>
    <input type="text" name="lånant_telefon" required>
    
    <label>Lånedatum:</label>
    <input type="date" name="lånedatum" value="<?= date('Y-m-d') ?>" required>
    
    <button type="submit">Låna ut</button>
  </form>

  <img src="<?= htmlspecialchars($product['bild']) ?>" alt="Bild på produkten" class="product-image">
  
  <a href="index.php">Tillbaka</a>
</body>
</html>
