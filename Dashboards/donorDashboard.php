<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure donor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// One-time welcome alert for new donors
if (isset($_SESSION['is_new_donor'])) {
    echo "<script>alert('Welcome! Please update your password from your profile.');</script>";
    unset($_SESSION['is_new_donor']);
}

$loggedInUserId = $_SESSION['user_id'];
$donorId = null;
$donorName = "Donor";
$recentDonations = [];
$totalCompletedAmount = 0;
$completedDonationCount = 0;
$lastCompletedDate = 'N/A';

// Get donor_Id and full_name from donors table using user_id
$stmt = $conn->prepare("SELECT donor_Id, full_name FROM donors WHERE user_id = ?");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $donorId = $row['donor_Id'];
    $donorName = $row['full_name'];
}
$stmt->close();

if (!$donorId) {
    die("Donor profile not found.");
}

// Fetch all completed donations for accurate totals
$stmt = $conn->prepare("SELECT amount, donation_date FROM donations WHERE donor_Id = ? AND status = 'Completed'");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $totalCompletedAmount += $row['amount'];
    $completedDonationCount++;
    $lastCompletedDate = date("d M Y", strtotime($row['donation_date']));
}
$stmt->close();

// Fetch 5 most recent donations
$stmt = $conn->prepare("
    SELECT d.donation_Id, d.donation_date, c.campaign_name, d.amount, d.status, d.mpesa_receipt,
           COALESCE(s.school_name, 'N/A') AS school_name
    FROM donations d
    JOIN campaigns c ON d.campaign_id = c.campaign_id
    LEFT JOIN school_profiles s ON c.schoolAdmin_id = s.schoolAdmin_id
    WHERE d.donor_Id = ?
    ORDER BY d.donation_date DESC
    
");

if ($stmt) {
    $stmt->bind_param("i", $donorId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recentDonations[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/adminfooter.css">
  <link rel="stylesheet" href="../CSS/donorDashboard.css">
</head>
<body>

<!-- Sidebar toggle button for mobile -->
<button class="toggle-sidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <a href="#" class="fw-bold fs-5 text-white px-4 mb-3 d-block">EduFund Donor</a>
  <a href="../Pages/Campaign.php"><i class="fas fa-search me-2"></i> Search Campaigns</a>
  <a href="../Pages/Donationhistory.php"><i class="fas fa-clock me-2"></i> Donation History</a>
  <a href="../Pages/Leaderboard.php"><i class="fas fa-trophy me-2"></i> Leaderboard</a>
  <a href="../Pages/donorReports.php"><i class="fas fa-chart-line me-2"></i> Fund Reports</a>
  <a href="../mpesa/myreceipts.php"><i class="fas fa-file-invoice me-2"></i> Receipt</a>
  <a href="../includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <?php
  $breadcrumbs = []; // Optional breadcrumbs
  include_once("../Templates/breadcrumb.php");
  ?>

  <h2 class="fw-bold mb-4 text-primary">Welcome, <?= htmlspecialchars($donorName) ?>!</h2>

  <!-- Summary Widgets -->
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="summary-widget bg-navy text-white rounded-3 p-4 shadow-sm">
        <h6>Total Donated</h6>
        <h4>KES <?= number_format($totalCompletedAmount, 2) ?></h4>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-widget bg-navy text-white rounded-3 p-4 shadow-sm">
        <h6>Number of Donations</h6>
        <h4><?= $completedDonationCount ?></h4>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-widget bg-navy text-white rounded-3 p-4 shadow-sm">
        <h6>Last Donation Date</h6>
        <h4><?= $lastCompletedDate ?></h4>
      </div>
    </div>
  </div>

  <!-- Recent Donations -->
  <div class="container recent-donations-section shadow-lg rounded-4 p-4 bg-white mb-5">
    <h4 class="fw-bold mb-4 text-primary border-bottom pb-2"><i class="fas fa-hand-holding-heart me-2"></i>Recent Donations</h4>

    <?php if (count($recentDonations) > 0): ?>
      <div class="row g-4">
        <?php foreach ($recentDonations as $donation): ?>
          <?php
            $status = $donation['status'];
            $campaignName = htmlspecialchars($donation['campaign_name']);
            $hideReceipt = (stripos($campaignName, 'digital devices') !== false);
          ?>
          <div class="col-md-6">
            <div class="p-4 rounded-3 donation-card h-100">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="mb-1 fw-semibold"><?= $campaignName ?></h6>
                  <small><i class="fas fa-school me-1"></i><?= htmlspecialchars($donation['school_name']) ?></small>
                </div>
                <div class="text-end">
                  <span class="badge bg-primary">KES <?= number_format($donation['amount'], 2) ?></span><br>
                  <span class="badge <?= $status === 'Completed' ? 'bg-success' : ($status === 'Failed' ? 'bg-danger' : 'bg-warning text-dark') ?> mt-2">
                    <?= htmlspecialchars($status) ?>
                  </span>
                </div>
              </div>
              <?php if ($status === 'Completed' && !$hideReceipt && !empty($donation['mpesa_receipt'])): ?>
                <div class="mt-3 text-end">
                  <a href="../mpesa/receipt_pdf.php?donation_Id=<?= $donation['donation_Id'] ?>" class="btn btn-sm btn-outline-light">Download Receipt</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted text-center mt-3">You haven't made any donations yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php include_once("../Templates/Admindashboard.php"); ?>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
  }
</script>

</body>
</html>



