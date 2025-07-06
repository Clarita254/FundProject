<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure donor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/SignIn.php");
    exit();
}

// Filtering inputs
$schoolFilter = isset($_GET['school']) ? trim($_GET['school']) : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$reportsPerPage = 4;
$offset = ($page - 1) * $reportsPerPage;

$filterQuery = "WHERE 1=1";
$params = [];
$types = "";

if ($schoolFilter !== '') {
    $filterQuery .= " AND r.school_name LIKE ?";
    $params[] = "%$schoolFilter%";
    $types .= "s";
}
if ($dateFilter !== '') {
    $filterQuery .= " AND r.report_date = ?";
    $params[] = $dateFilter;
    $types .= "s";
}

$totalQuery = "SELECT COUNT(*) FROM progress_reports r $filterQuery";
$stmt = $conn->prepare($totalQuery);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->bind_result($totalReports);
$stmt->fetch();
$stmt->close();
$totalPages = ceil($totalReports / $reportsPerPage);

$reports = [];
$query = "SELECT r.*, u.username AS admin_name FROM progress_reports r 
          JOIN users u ON r.schoolAdmin_id = u.user_id 
          $filterQuery ORDER BY r.report_date DESC LIMIT ? OFFSET ?";
$params[] = $reportsPerPage;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fund Utilization Reports</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/donorReports.css">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container">
  <h2 class="page-title">Fund Utilization Reports</h2>

  <form method="GET" class="row mb-4">
    <div class="col-md-5">
      <input type="text" name="school" class="form-control" placeholder="Filter by School Name" value="<?= htmlspecialchars($schoolFilter) ?>">
    </div>
    <div class="col-md-4">
      <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($dateFilter) ?>">
    </div>
    <div class="col-md-3">
      <button type="submit" class="form-control">Filter</button>
    </div>
  </form>

  <?php if (count($reports) > 0): ?>
    <div class="row g-4">
      <?php foreach ($reports as $report): ?>
        <div class="col-md-6">
          <div class="report-card">
            <div class="report-title"><?= htmlspecialchars($report['title']) ?></div>
            <div class="report-meta">
              <strong>School:</strong> <?= htmlspecialchars($report['school_name']) ?><br>
              <strong>Date:</strong> <?= date("d M Y", strtotime($report['report_date'])) ?><br>
              <strong>By:</strong> <?= htmlspecialchars($report['admin_name']) ?>
            </div>
            <div class="amount-used">Used: KES <?= number_format($report['amount_used'], 2) ?></div>
            <div class="report-description"><?= htmlspecialchars($report['description']) ?></div>
            <?php if (!empty($report['photos'])): ?>
              <div class="photo-thumbnails d-flex flex-wrap">
                <?php foreach (explode(",", $report['photos']) as $photo): ?>
                  <img src="<?= htmlspecialchars($photo) ?>"class="thumbnail me-2 mb-2" alt="Progress Photo">
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&school=<?= urlencode($schoolFilter) ?>&date=<?= urlencode($dateFilter) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>

  <?php else: ?>
    <p class="text-muted">No progress reports match your filter criteria.</p>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>