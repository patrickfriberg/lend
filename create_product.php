<?php
include 'db.php';

// Hämta alla kategorier
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Skapa Produkt</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Skapa Produkt</h1>
  
  <form action="process_create.php" method="post" enctype="multipart/form-data">
  <label>Kategori:</label>
    <select name="category_id" required>
      <option value="">Välj kategori</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
      <?php endforeach; ?>
    </select>
    
    <label>Storlek/Längd:</label>
    <input type="text" name="storlek" required>
    
    <label>Kvalité:</label>
    <input type="text" name="kvalite" required>
    <!-- Platsfältet tas bort eftersom platsen hanteras automatiskt -->
    <?php if ($_SESSION['role'] === 'moderator'): ?>
    <input type="hidden" name="location_id" value="<?= $_SESSION['location_id'] ?>">
<?php endif; ?>
<?php if ($_SESSION['role'] === 'admin'): ?>
    <label>Plats:</label>
    <select name="location_id">
        <?php
        $stmt = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
        while ($row = $stmt->fetch()) {
            echo "<option value='{$row['id']}'>{$row['location_name']}</option>";
        }
        ?>
    </select>
<?php endif; ?>
    
    <label>Bild:</label>
    <input type="file" name="bild" accept="image/*">
    
    <button type="submit">Skapa</button>
  </form>
  <?php if (isset($_GET['success'])): ?>
  <p>Produkten har lagts till!</p>
<?php endif; ?>
  
  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
