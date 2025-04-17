<?php
include 'db.php';

// Ställ in rätt HTTP-headers för att skicka en CSV-fil till webbläsaren
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="loan_statistics.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['År', 'Månad', 'Antal utlån']); // Rubriker

// Hämta statistik från databasen
$stmt = $pdo->query("SELECT YEAR(lånedatum) AS år, MONTH(lånedatum) AS månad, COUNT(*) AS antal 
                     FROM loans GROUP BY YEAR(lånedatum), MONTH(lånedatum) ORDER BY år ASC, månad ASC");
$loan_stats = $stmt->fetchAll();

foreach ($loan_stats as $stat) {
    fputcsv($output, [$stat['år'], $stat['månad'], $stat['antal']]);
}

fclose($output);
exit;
?>
