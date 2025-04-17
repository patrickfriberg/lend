<?php
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Kategori ej hittad.");
}

// Hämta befintlig kategori
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST["category_name"]);
    if (!empty($new_name)) {
        $stmt = $pdo->prepare("UPDATE categories SET category_name = ? WHERE id = ?");
        $stmt->execute([$new_name, $id]);
        header("Location: manage_categories.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Redigera Kategori</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Redigera Kategori</h1>

  <form method="post">
    <label>Nytt kategorinamn:</label>
    <input type="text" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>" required>
    <button type="submit">Spara ändringar</button>
  </form>

  <a href="manage_categories.php">Tillbaka</a>
</body>
</html>
