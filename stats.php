<?php
include 'db.php';

// Ange vilken typ av statistik du vill visa: "total" eller "year"
$type = 'total'; // Ändra till 'year' om du bara vill visa årets lån

// Ange vilken plats du vill visa statistik för (t.ex. 1, 2, 3)
$location_id = ''; // Ändra detta värde för att filtrera efter plats

// Skapa grundfrågan för att hämta antal lån
$query = "SELECT COUNT(*) FROM loans WHERE 1";

// Filtrera efter år om det är valt
if ($type === 'year') {
    $query .= " AND YEAR(loan_date) = YEAR(CURRENT_DATE)";
}

// Filtrera efter plats om ett plats-ID är angivet
if (!empty($location_id)) {
    $query .= " AND location_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$location_id]);
} else {
    $stmt = $pdo->query($query);
}

// Hämta resultatet
$count = $stmt->fetchColumn();

// Svara med JSON
header('Content-Type: application/json');
echo json_encode(['loans' => $count]);
?>
