<?php
include 'db.php';

//Platsdata
$selected_location = $_GET['location'] ?? null;

// Hämta alla unika år
$stmt = $pdo->query("SELECT DISTINCT YEAR(lånedatum) AS år FROM loans ORDER BY år DESC");
$years = $stmt->fetchAll();

$selected_year = $_GET['year'] ?? null;

// Hämta totala antal utlån per år
$query = "SELECT YEAR(lånedatum) AS år, COUNT(*) AS totalt_antal FROM loans WHERE 1";

$params = [];
if ($selected_year) {
    $query .= " AND YEAR(lånedatum) = ?";
    $params[] = $selected_year;
}
if ($selected_location) {
    $query .= " AND location_id = ?";
    $params[] = $selected_location;
}

$query .= " GROUP BY år ORDER BY år ASC";

$total_stmt = $pdo->prepare($query);
$total_stmt->execute($params);
$total_loans = $total_stmt->fetchAll();
$total_for_selected_year = 0;
foreach ($total_loans as $year_data) {
  if ($selected_year && $year_data['år'] == $selected_year) {
      $total_for_selected_year = $year_data['totalt_antal'];
  }
}

// Hämta statistik per månad, filtrerat per år om ett val gjorts
$query = "SELECT YEAR(lånedatum) AS år, MONTH(lånedatum) AS månad, COUNT(*) AS antal 
          FROM loans WHERE 1";

$params = [];
if ($selected_year) {
    $query .= " AND YEAR(lånedatum) = ?";
    $params[] = $selected_year;
}
if ($selected_location) {
    $query .= " AND location_id = ?";
    $params[] = $selected_location;
}

$stmt = $pdo->prepare($query . " GROUP BY YEAR(lånedatum), MONTH(lånedatum) ORDER BY år ASC, månad ASC");
$stmt->execute($params);
$loan_stats = $stmt->fetchAll();

//Mest populära produkterna är
$query = "SELECT c.category_name, COUNT(l.id) AS antal_lån 
    FROM loans l
    JOIN products p ON l.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    WHERE 1";

$params = [];
if ($selected_location) {
    $query .= " AND l.location_id = ?";
    $params[] = $selected_location;
}

$query .= " GROUP BY c.category_name ORDER BY antal_lån DESC LIMIT 5";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$popular_categories = $stmt->fetchAll();
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Utlåningsstatistik</title>
  <link rel="stylesheet" href="styles.css">
</head>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<body>
  <h1>Utlåningsstatistik</h1>
  

  <form method="get" action="loan_statistics.php">
    <label>Välj plats:</label>
    <select name="location">
        <option value="">Alla platser</option>
        <?php
        $stmt = $pdo->query("SELECT * FROM locations ORDER BY location_name ASC");
        while ($location = $stmt->fetch()):
        ?>
            <option value="<?= $location['id'] ?>" <?= ($selected_location == $location['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($location['location_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Filtrera</button>
</form>
  <!-- Filter för att välja ett år -->
  <form method="get" action="loan_statistics.php">
    <label>Välj år:</label>
    <select name="year" onchange="this.form.submit()">
      <option value="">Alla år</option>
      <?php foreach ($years as $year): ?>
        <option value="<?= $year['år'] ?>" <?= ($selected_year == $year['år']) ? 'selected' : '' ?>>
          <?= $year['år'] ?>
        </option>
      <?php endforeach; ?>
    </select>
    <input type="hidden" name="location" value="<?= htmlspecialchars($selected_location) ?>">
  </form>

  <!-- Visa totalt antal utlån för det valda året -->
  <?php if ($selected_year): ?>
    <h2>Totalt antal utlån för år <?= htmlspecialchars($selected_year) ?>: <?= htmlspecialchars($total_for_selected_year) ?></h2>
  <?php else: ?>
    <h2>Totalt antal utlån per år:</h2>
    <ul>
      <?php foreach ($total_loans as $year_data): ?>
        <li><?= htmlspecialchars($year_data['år']) ?>: <?= htmlspecialchars($year_data['totalt_antal']) ?> utlån</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h2>Utlåningsstatistik per Månad</h2>
  <form method="get" action="export_csv.php">
  <button type="submit">Exportera till CSV</button>
</form>
  <table>
    <thead>
      <tr>
        <th>År</th>
        <th>Månad</th>
        <th>Antal utlån</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($loan_stats as $stat): ?>
      <tr>
        <td><?= htmlspecialchars($stat['år']) ?></td>
        <?php
$months = [
    "January" => "Januari", "February" => "Februari", "March" => "Mars",
    "April" => "April", "May" => "Maj", "June" => "Juni",
    "July" => "Juli", "August" => "Augusti", "September" => "September",
    "October" => "Oktober", "November" => "November", "December" => "December"
];

$englishMonth = date("F", mktime(0, 0, 0, $stat['månad'], 1)); // Månaden på engelska
$swedishMonth = $months[$englishMonth]; // Konvertera till svenska
?>
<td><?= htmlspecialchars($swedishMonth) ?></td>
        <td><?= htmlspecialchars($stat['antal']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <h2>Mest populära produktkategorier</h2>
<ul>
<?php foreach ($popular_categories as $category): ?>
        <li><?= htmlspecialchars($category['category_name']) ?> – <?= htmlspecialchars($category['antal_lån']) ?> lån</li>
    <?php endforeach; ?>
</ul>
      </br>
      <label>Välj diagramtyp:</label>
<select id="chartType" onchange="updateChartType()">
  <option value="bar">Stapeldiagram</option>
  <option value="line">Linjediagram</option>
  <option value="pie">Cirkeldiagram</option>
</select>
      </br>
  <canvas id="loanChart"></canvas>
      </br>
      </br>
  <a href="index.php" class="back-button">Tillbaka</a>
       
<script>
const loanData = <?= json_encode($loan_stats) ?>;

const monthNames = [
    "Januari", "Februari", "Mars", "April", "Maj", "Juni",
    "Juli", "Augusti", "September", "Oktober", "November", "December"
];
const labels = loanData.map(stat => `${monthNames[stat.månad - 1]} ${stat.år}`);

const values = loanData.map(stat => stat.antal);
function generateColors(count) {
    let colors = [];
    for (let i = 0; i < count; i++) {
        let hue = (i * (360 / count)) % 360;  // Sprider färger jämnt över hela färghjulet
        colors.push(`hsl(${hue}, 70%, 60%)`); // Använd HSL för fler nyanser
    }
    return colors;
}
const ctx = document.getElementById('loanChart').getContext('2d');
let loanChart = new Chart(ctx, {
    type: 'bar', // Standarddiagram
    data: {
        labels: labels,
        datasets: [{
            label: 'Antal utlån per månad',
            data: values,
            backgroundColor: ['rgba(54, 162, 235, 0.6)'],
            borderColor: ['rgba(54, 162, 235, 1)'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function updateChartType() {
    const selectedType = document.getElementById('chartType').value;
    
    // Förstör det gamla diagrammet innan vi skapar ett nytt
    loanChart.destroy();

    loanChart = new Chart(ctx, {
        type: selectedType,
        data: {
            labels: labels,
            datasets: [{
                label: 'Antal utlån per månad',
                data: values,
                backgroundColor: selectedType === 'pie' ? generateColors(labels.length) : ['rgba(54, 162, 235, 0.6)'],
                borderColor: selectedType === 'pie' ? generateColors(labels.length).map(color => color.replace('60%', '40%')) : ['rgba(54, 162, 235, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 800,  // Gör animationen lite långsammare (standard är 400ms)
                easing: 'easeOutQuart'  // Gör den mjukare
            },
            scales: selectedType !== 'pie' ? { y: { beginAtZero: true } } : {}
        }
    });
}
</script>

</body>
</html>
