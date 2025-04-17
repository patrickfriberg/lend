<?php
include 'db.php';  // Kontakta databasen

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $location_id = $_POST['location_id'];

    // Hasha lösenordet
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Preppa SQL
    $stmt = $pdo->prepare("INSERT INTO users (username, password, location_id) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$username, $hash, $location_id]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        // Annars
        die("Registration failed: " . $e->getMessage());
    }
}
?>
