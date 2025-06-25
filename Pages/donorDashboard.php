<?php
session_start();
require_once("../includes/db_connect.php");

// Access Control: Only donors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$donorId = $_SESSION['user_id'];
$donorName = $_SESSION['username'] ?? 'Donor';

// Fetch recent donations
$stmt = $conn->prepare("
    SELECT d.donation_date, c.campaign_name, d.amount 
    FROM donations d
    JOIN campaigns c ON d.campaign_id = c.campaign_id
    WHERE d.donor_id = ?
    ORDER BY d.donation_date DESC
    LIMIT 5
");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
$recentDonations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <style>
    .dashboard-card {
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
  </style>
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container py-5">
  <h2 class="fw-bold mb-4 text-center text-primary">Welcome, <?= htmlspecialchars($donorName) ?>!</h2>

  <!-- Quick Actions -->
  <div class="row text-center mb-4">
    <div class="col-md-4 mb-3">
      <a href="campaigns.php" class="btn btn-outline-primary w-100"><i class="fas fa-search me-2"></i>Search Campaigns</a>
    </div>
    <div class="col-md-4 mb-3">
      <a href="donationHistory.php" class="btn btn-outline-success w-100"><i class="fas fa-clock me-2"></i>View Donation History</a>
    </div>
    <div class="col-md-4 mb-3">
      <a href="leaderboard.php" class="btn btn-outline-warning w-100"><i class="fas fa-trophy me-2"></i>Leaderboard</a>
    </div>
  </div>

  <!-- Recent Donations -->
  <div class="card dashboard-card p-4 mb-5">
    <h5 class="fw-bold mb-3">Recent Donations</h5>
    <?php if (count($recentDonations) > 0): ?>
      <ul class="list-group">
        <?php foreach ($recentDonations as $donation): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($donation['campaign_name']) ?>
            <span class="badge bg-primary rounded-pill">KES <?= number_format($donation['amount'], 2) ?></span>
            <small><?= date('d M Y', strtotime($donation['donation_date'])) ?></small>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">You haven't made any donations yet.</p>
    <?php endif; ?>
  </div>

  <!-- Logout -->
  <div class="text-center">
    <a href="../includes/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
