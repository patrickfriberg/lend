<?php
session_start();
include 'db.php';

// Kontrollera om användaren är inloggad och har adminstatus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Om ej admin → skicka tillbaka till startsidan
    exit;
}
//Hämta icke godkända användare
$users = $pdo->query("SELECT id, username FROM users WHERE approved = 0")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin panelen</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.png">
</head>
<body><h1>Administrationspanel</h1>
<a href="manage_categories.php" class="back-button">Kategorier</a>
<h2>Väntande registreringar</h2>
<form method="post" action="approve_user.php">
    <?php foreach ($users as $user): ?>
        <input type="checkbox" name="approved[]" value="<?= $user['id'] ?>"> <?= $user['username'] ?><br>
    <?php endforeach; ?>
    <button type="submit">Godkänn valda användare</button>
</form>
<?php if (isset($_GET['approved'])): ?>
  <p>✅ Användare godkända!</p>
<?php endif; ?>
<h2>Skapa plats</h2>
<form method="post" action="create_location.php">
  <label>Platsnamn:</label>
  <input type="text" name="location_name" required>
  
  <label>Adress:</label>
  <input type="text" name="address">
  
  <label>Stad:</label>
  <input type="text" name="city">
  
  <label>Land:</label>
  <input type="text" name="country">

  <button type="submit">Skapa plats</button>
</form>
<h2>Hantera användarroller</h2>
<table>
  <thead>
    <tr>
      <th>Användarnamn</th>
      <th>Nuvarande roll</th>
      <th>Ny roll</th>
      <th>Åtgärd</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY role ASC");

    while ($user = $stmt->fetch()): ?>
      <tr>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['role']) ?></td>
        <td>
          <form method="post" action="change_user_role.php">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <select name="new_role">
              <option value="user">User</option>
              <option value="moderator">Moderator</option>
              <option value="admin">Admin</option>
            </select>
            <button type="submit">Ändra</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<h2>Flytta användare mellan platser</h2>
<table>
  <thead>
    <tr>
      <th>Användarnamn</th>
      <th>Nuvarande Plats</th>
      <th>Ny Plats</th>
      <th>Åtgärd</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $pdo->query("SELECT users.id, users.username, locations.location_name FROM users 
                         JOIN locations ON users.location_id = locations.id");

    while ($user = $stmt->fetch()): ?>
      <tr>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['location_name']) ?></td>
        <td>
          <form method="post" action="change_user_location.php">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <select name="new_location_id">
              <?php
              $stmt_loc = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
              while ($loc = $stmt_loc->fetch()) {
                  echo "<option value='{$loc['id']}'>{$loc['location_name']}</option>";
              }
              ?>
            </select>
            <button type="submit">Flytta</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<a href="index.php" class="back-button">Tillbaka</a>
</br></br>
<div><form action="export_database.php" method="post">
    <button type="submit" class="export-button">Exportera Databas</button>
</form></div>

<h2>Senaste systemändringar</h2>
<table>
  <thead>
    <tr>
      <th>Användare</th>
      <th>Händelse</th>
      <th>Tid</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $pdo->query("SELECT * FROM logs ORDER BY timestamp DESC LIMIT 10");
    while ($log = $stmt->fetch()):
    ?>
      <tr>
        <td><?= htmlspecialchars($log['username']) ?></td>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= $log['timestamp'] ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>


</body>
</html>