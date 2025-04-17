<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Hämta användare från databasen
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Om användaren fanns och lösenordet stämde
    if ($user && password_verify($password, $user['password'])) {
        if ($user['approved'] == 1){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Lägg till roll i sessionen
        $_SESSION['location_id'] = $user['location_id']; // Lägg till plats
        } 
        else{
            header("Location: login.php?nonapproved=1");
            exit;
            //echo "🔒 Ditt konto väntar på godkännande från administratören.";
        }
    
        header("Location: index.php");
        exit;
    } 
    else {
        $_SESSION['error'] = "Fel användarnamn eller lösenord.";
        header("Location: login.php");
        exit;
    }
    
}
?>
