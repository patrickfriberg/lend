<?php
include 'db.php';

// Hämta alla produkter med status 'utlånad'
$stmt = $pdo->prepare("SELECT * FROM products WHERE status = 'utlånad'");
$stmt->execute();
$products = $stmt->fetchAll();

$currentDate = new DateTime();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Utlånade Artiklar</title>
  <link rel="stylesheet" href="styles.css">
  
</head>
<body>
  <h1>Utlånade Artiklar</h1>
  <div>Rödmarkerade produkter har varit utlånade mer än 14 dagar.</div>
</br>
<?php if (isset($_GET['success'])): ?>
  <p>Produkten har lånats ut och bör finnas i listan här under!</p>
<?php endif; ?>
<?php if (isset($_GET['returned'])): ?>
  <p>Produkten har lämnats tillbaka!</p>
<?php endif; ?>
  <?php if ($products): ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Typ</th>
        <th>Storlek</th>
        <th>Kvalité</th>
        <th>Lånat av</th>
        <th>Telefon</th>
        <th>Lånedatum</th>
        <th>Status</th>
        <th>Återlämna</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $prod): ?>
        <?php 
          // Kolla om produkten är utlånad längre än två veckor
          $loanDate = new DateTime($prod['lånedatum']);
          $interval = $loanDate->diff($currentDate);
          $overdue = ($interval->days > 14);
        ?>
        <tr class="<?= $overdue ? 'overdue' : '' ?>">
          <td><?= $prod['id'] ?></td>
          <td><?= htmlspecialchars($prod['typ']) ?></td>
          <td><?= htmlspecialchars($prod['storlek']) ?></td>
          <td><?= htmlspecialchars($prod['kvalite']) ?></td>
          <td><?= htmlspecialchars($prod['lånant_namn']) ?></td>
          <td><?= htmlspecialchars($prod['lånant_telefon']) ?></td>
          <td><?= htmlspecialchars($prod['lånedatum']) ?></td>
          <td><?= htmlspecialchars($prod['status']) ?></td>
          <td><?php if ($prod['status'] == "inne"): ?>
              <a href="lend_product.php?id=<?= $prod['id'] ?>">Låna ut</a>
            <?php else: ?>
              <a href="return_product.php?id=<?= $prod['id'] ?>" class="returnlink">Återlämna</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>Inga utlånade produkter hittades.</p>
  <?php endif; ?>

  
  
  <a href="index.php" class="back-button">Tillbaka</a>
</body>
</html>
