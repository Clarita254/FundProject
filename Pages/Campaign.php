<?php
session_start();
require_once("../includes/db_connect.php");

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'guest';

// Get filters
$category = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');
$goalMin = $_GET['goal_min'] ?? '';
$goalMax = $_GET['goal_max'] ?? '';
$dateStart = $_GET['date_start'] ?? '';
$dateEnd = $_GET['date_end'] ?? '';
$schoolName = $_GET['school_name'] ?? '';

// Build SQL query
$query = "SELECT c.*, u.username AS school_name FROM campaigns c 
          JOIN users u ON c.schoolAdmin_id = u.user_id
          WHERE c.status = 'Approved'";

if (!empty($category)) {
    $query .= " AND c.category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if (!empty($search)) {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $query .= " AND (c.campaign_name LIKE '%$escapedSearch%' OR c.description LIKE '%$escapedSearch%')";
}
if (is_numeric($goalMin)) {
    $query .= " AND c.target_amount >= " . (float)$goalMin;
}
if (is_numeric($goalMax)) {
    $query .= " AND c.target_amount <= " . (float)$goalMax;
}
if (!empty($dateStart)) {
    $query .= " AND c.start_date >= '" . mysqli_real_escape_string($conn, $dateStart) . "'";
}
if (!empty($dateEnd)) {
    $query .= " AND c.end_date <= '" . mysqli_real_escape_string($conn, $dateEnd) . "'";
}
if (!empty($schoolName)) {
    $query .= " AND u.username LIKE '%" . mysqli_real_escape_string($conn, $schoolName) . "%'";
}

$query .= " ORDER BY c.start_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Filtered Campaigns</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="../CSS/Footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/Campaign.css">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="container py-5">
  <h1 class="text-center mb-4">Explore Campaigns</h1>

  <!-- Filter Form -->
  <form method="GET" class="mb-4 px-3">
    <div class="row g-3">
      <div class="col-md-3">
        <input type="text" name="search" placeholder="Search..." class="form-control" value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-2">
        <select name="category" class="form-select">
          <option value="">All Categories</option>
          <option value="infrastructure" <?= $category == 'infrastructure' ? 'selected' : '' ?>>Infrastructure</option>
          <!-- Add other category options -->
        </select>
      </div>
      <div class="col-md-2">
        <input type="number" name="goal_min" placeholder="Min Goal" class="form-control" value="<?= htmlspecialchars($goalMin) ?>">
      </div>
      <div class="col-md-2">
        <input type="number" name="goal_max" placeholder="Max Goal" class="form-control" value="<?= htmlspecialchars($goalMax) ?>">
      </div>
      <div class="col-md-2">
        <input type="text" name="school_name" placeholder="School Name" class="form-control" value="<?= htmlspecialchars($schoolName) ?>">
      </div>
      <div class="col-md-1">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
      </div>
    </div>
  </form>

  <!-- Campaign Cards -->
  <div class="campaigns-container">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
          $title = htmlspecialchars($row['campaign_name']);
          $desc = htmlspecialchars($row['description']);
          $goal = $row['target_amount'];
          $raised = $row['amount_raised'];
          $school = htmlspecialchars($row['school_name']);
          $daysLeft = ceil((strtotime($row['end_date']) - time()) / 86400);
          $progress = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
          $image = (!empty($row['image_path']) && file_exists("../" . $row['image_path']))
            ? "../" . $row['image_path']
            : "https://via.placeholder.com/300x200";
        ?>
        <div class="campaign-card">
          <div class="campaign-image" style="background-image: url('<?= $image ?>')"></div>
          <div class="campaign-content">
            <h3><?= $title ?> <span class="badge bg-secondary"><?= $school ?></span></h3>
            <p><?= $desc ?></p>
            <div class="progress mb-2">
              <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress ?>%;"></div>
            </div>
            <p>Raised: KES <?= number_format($raised, 2) ?> of <?= number_format($goal, 2) ?></p>
            <a href="Donations.php?campaign_id=<?= $row['id'] ?>" class="btn btn-success">Donate</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center text-muted">No campaigns match your filters.</p>
    <?php endif; ?>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
