<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only logged-in school admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];
$progressReports = [];

// Fetch progress reports submitted by the current school admin
$stmt = $conn->prepare("SELECT * FROM progress_reports WHERE schoolAdmin_id = ? ORDER BY report_date DESC");
$stmt->bind_param("i", $schoolAdminId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $progressReports[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submitted Progress Reports</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/progressReports.css">

</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container py-5">
  <h2 class="text-primary fw-bold mb-4">My Fund Utilization Reports</h2>

  <?php if (count($progressReports) > 0): ?>
    <div class="row g-4">
      <?php foreach ($progressReports as $report): ?>
        <div class="col-md-6">
          <div class="report-card p-4 rounded shadow">
            <h5 class="fw-semibold mb-2"><?= htmlspecialchars($report['title']) ?></h5>
            <p class="mb-1"><strong>Date:</strong> <?= date("d M Y", strtotime($report['report_date'])) ?></p>
            <p class="mb-1"><strong>Amount Used:</strong> KES <?= number_format($report['amount_used'], 2) ?></p>
            <p class="mb-2"><strong>Description:</strong> <?= htmlspecialchars($report['description']) ?></p>
            <?php if (!empty($report['photos'])): ?>
              <div class="photo-thumbnails d-flex flex-wrap">
                <?php foreach (explode(",", $report['photos']) as $photo): ?>
                  <img src="<?= htmlspecialchars($photo) ?>" class="thumbnail me-2 mb-2" alt="Progress Photo">


                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted">You haven’t submitted any progress reports yet.</p>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
