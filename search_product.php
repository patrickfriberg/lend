<?php
include 'db.php';

// Hämta alla platser och kategorier
$stmt_locations = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
$locations = $stmt_locations->fetchAll();

$stmt_categories = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt_categories->fetchAll();

$location_id = $_GET['location_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;
$search_term = $_GET['search'] ?? null;
$sort_order = $_GET['sort_order'] ?? 'ASC'; // Standardvärde: stigande ordning

$query = "SELECT products.*, locations.location_name, categories.category_name 
          FROM products 
          JOIN locations ON products.location_id = locations.id 
          JOIN categories ON products.category_id = categories.id 
          WHERE 1";

$params = [];

// Filtrera per plats
if ($location_id) {
    $query .= " AND products.location_id = ?";
    $params[] = $location_id;
}

// Filtrera per kategori
if ($category_id) {
    $query .= " AND products.category_id = ?";
    $params[] = $category_id;
}

// Sök på produktens namn eller egenskaper
/*if ($search_term) {
    $query .= " AND (products.id = ? OR products.storlek LIKE ? OR products.kvalite LIKE ?)";
    $params[] = $search_term;
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}*/
//Sök på ID, eller kvalite. Storlek går inte med den här funktionen men med ovanstående
if (ctype_digit($search_term)) {
  // Om söktermen är ett heltal, sök endast på ID
  $query .= " AND products.id = ?";
  $params[] = $search_term;
} else {
  // Om söktermen är text, sök på storlek och kvalitet
  $query .= " AND (products.storlek LIKE ? OR products.kvalite LIKE ?)";
  $params[] = "%$search_term%";
  $params[] = "%$search_term%";
}

// Sortera efter storlek
$query .= " ORDER BY products.id $sort_order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Sök Produkt</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Sök Produkter</h1>
  
  <form method="get" action="search_product.php">
    <label>Plats:</label>
    <select name="location_id">
      <option value="">Alla platser</option>
      <?php foreach ($locations as $location): ?>
        <option value="<?= $location['id'] ?>" <?= ($location_id == $location['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($location['location_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Kategori:</label>
    <select name="category_id">
      <option value="">Alla kategorier</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category['id'] ?>" <?= ($category_id == $category['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($category['category_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Sökord:</label>
    <input type="text" name="search" value="<?= htmlspecialchars($search_term) ?>" placeholder="Exempel: storlek, kvalité">
    
    <label>Sortera:</label>
    <select name="sort_order">
      <option value="ASC" <?= ($sort_order == 'ASC') ? 'selected' : '' ?>>Stigande</option>
      <option value="DESC" <?= ($sort_order == 'DESC') ? 'selected' : '' ?>>Fallande</option>
    </select>

    <button type="submit">Sök</button>
  </form>

  <h2>Resultat:</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Plats</th>
        <th>Kategori</th>
        <th>Storlek/längd</th>
        <th>Kvalité</th>
        <th>Ta bort</th>
        <th>Låna ut/Återlämna</th>
        
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $prod): ?>
      <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['location_name']) ?></td>
        <td><?= htmlspecialchars($prod['category_name']) ?></td>
        <td><?= htmlspecialchars($prod['storlek']) ?></td>
        <td><?= htmlspecialchars($prod['kvalite']) ?></td>
        <td>
        <?php if ($_SESSION['role'] === 'admin' || ($_SESSION['role'] === 'moderator' && $_SESSION['location_id'] === $prod['location_id'])): ?>
        <a href="delete_product.php?id=<?= $prod['id'] ?>" class="delete-button" onclick="return confirm('Är du säker på att du vill ta bort denna produkt?')">Ta bort</a>
    <?php endif; ?>
        </td>
    <td>
            <?php if ($prod['status'] == "inne"): ?>
              <a href="lend_product.php?id=<?= $prod['id'] ?>" class="loan-button">Låna ut</a>
            <?php else: ?>
              <a href="return_product.php?id=<?= $prod['id'] ?>" class="returnlink">Återlämna</a>
            <?php endif; ?>
            
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (isset($_GET['deleted'])): ?>
    <p>Produkten har tagits bort!</p>
<?php endif; ?>

  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
