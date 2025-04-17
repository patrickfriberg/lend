<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_category = trim($_POST["category_name"]);
    if (!empty($new_category)) {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
        try {
            $stmt->execute([$new_category]);
            header("Location: manage_categories.php");
            exit;
        } catch (PDOException $e) {
            echo "Fel vid skapandet av kategori: " . $e->getMessage();
        }
    }
}

// Hämta alla kategorier
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Hantera kategorier</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Hantera Kategorier</h1>
  
  <form method="post" action="manage_categories.php">
    <label>Ny kategori:</label>
    <input type="text" name="category_name" required>
    <button type="submit">Lägg till</button>
  </form>

  <h2>Existerande Kategorier:</h2>
  <ul>
    <?php foreach ($categories as $category): ?>
      <li class="lista"><?= htmlspecialchars($category['category_name']) ?>|
          <a href="edit_category.php?id=<?= $category['id'] ?>" class="delete-button">Redigera</a> |
          <a href="delete_category.php?id=<?= $category['id'] ?>" class="delete-button">Ta bort</a>
      </li>
    <?php endforeach; ?>
  </ul>
  
  <a href="admin.php" class="back-button">Tillbaka</a>
</body>
</html>
