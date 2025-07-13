<?php
session_start();
require_once("../includes/db_connect.php");



// Only schoolAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];

// Campaign ID must be provided
if (!isset($_GET['campaign_id'])) 
 {
    echo "<script>alert('Campaign ID not specified'); window.location.href='../Dashboards/manageCampaigns.php';</script>";
    exit();
}

$campaignId = intval($_GET['campaign_id']);

// Ensure campaign belongs to this schoolAdmin
$checkStmt = $conn->prepare("SELECT campaign_name FROM campaigns WHERE campaign_id = ? AND schoolAdmin_id = ?");
$checkStmt->bind_param("ii", $campaignId, $schoolAdminId);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo "<script>alert('Access denied or campaign not found'); window.location.href='../Dashboards/manageCampaigns.php';</script>";
    exit();
}
$checkStmt->bind_result($campaignTitle);
$checkStmt->fetch();
$checkStmt->close();


// Build query with filters
$filters = [];
$params = [$campaignId];
$types = 'i';

$sql = "SELECT d.amount, d.donation_date, d.payment_mode, d.status, u.username AS donor_name
        FROM donations d
        JOIN users u ON d.donor_id = u.user_id
        WHERE d.campaign_id = ?";

if (!empty($_GET['status'])) {
    $sql .= " AND d.status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

if (!empty($_GET['from'])) {
    $sql .= " AND d.donation_date >= ?";
    $params[] = $_GET['from'];
    $types .= 's';
}

if (!empty($_GET['to'])) {
    $sql .= " AND d.donation_date <= ?";
    $params[] = $_GET['to'];
    $types .= 's';
}

$sql .= " ORDER BY d.donation_date DESC";

// Pagination Setup
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Count total matching donations
$countQuery = "SELECT COUNT(*) FROM donations d WHERE d.campaign_id = ?";
$countTypes = 'i';
$countParams = [$campaignId];

if (!empty($_GET['status'])) {
    $countQuery .= " AND d.status = ?";
    $countParams[] = $_GET['status'];
    $countTypes .= 's';
}
if (!empty($_GET['from'])) {
    $countQuery .= " AND d.donation_date >= ?";
    $countParams[] = $_GET['from'];
    $countTypes .= 's';
}
if (!empty($_GET['to'])) {
    $countQuery .= " AND d.donation_date <= ?";
    $countParams[] = $_GET['to'];
    $countTypes .= 's';
}

$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param($countTypes, ...$countParams);
$countStmt->execute();
$countStmt->bind_result($totalRows);
$countStmt->fetch();
$countStmt->close();

$totalPages = ceil($totalRows / $perPage);

// Append LIMIT and OFFSET to SQL
$sql .= " LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$donations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalAmount = array_sum(array_column($donations, 'amount'));




?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donations for <?= htmlspecialchars($campaignTitle) ?></title>
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


  <style>
  body {
    background-color: #e9f2fb; /* soft blue background */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .donation-title {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 25px;
  }

  .donation-title h2 {
    color: #003366;
    margin: 0;
    font-weight: bold;
  }


  .btn-custom {
  background-color: #003366;
  color: #ffffff;
  border: 2px solid #b3d9ff; /* light blue outline */
  transition: background-color 0.3s, color 0.3s;
}

.btn-custom:hover {
  background-color: #002855; /* slightly darker on hover */
  border-color: #80c1ff;     /* slightly more visible border */
  color: #ffffff;
}

</style>

</head>
<body>
<?php include_once("../Templates/schoolAdminNav.php"); ?>

<div class="container mt-5">
<div class="donation-title">
  <h2>Donations for: <?= htmlspecialchars($campaignTitle) ?></h2>
</div>


<a href="../Dashboards/manageCampaigns.php" class="btn btn-custom mb-3">
  <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
</a>



  <!-- Filter Form -->
  <form method="GET" class="row g-2 mb-4">
    <input type="hidden" name="id" value="<?= $campaignId ?>">
    <div class="col-md-3">
      <label>Status</label>
      <select name="status" class="form-select">
        <option value="">All</option>
        <option value="Approved" <?= ($_GET['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
        <option value="Pending" <?= ($_GET['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Rejected" <?= ($_GET['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>
    <div class="col-md-3">
      <label>From</label>
      <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label>To</label>
      <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>" class="form-control">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
    </div>
  </form>

  <!-- Total Donations -->
  <div class="mb-3">
    <h5>Total Donations: <span class="text-success">KES <?= number_format($totalAmount, 2) ?></span></h5>

  </div>

  <?php if (count($donations) > 0): ?>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Donor Name</th>
          <th>Amount</th>
          <th>Payment Mode</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($donations as $donation): ?>
          <tr>
            <td><?= htmlspecialchars($donation['donor_name']) ?></td>
           <td>KES <?= number_format($donation['amount'], 2) ?></td>

            <td><?= htmlspecialchars($donation['payment_mode']) ?></td>
            <td>
              <span class="badge 
                <?= $donation['status'] === 'Approved' ? 'bg-success' : 
                    ($donation['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                <?= htmlspecialchars($donation['status']) ?>
              </span>
            </td>
            <td><?= date('d M Y', strtotime($donation['donation_date'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination -->
<nav aria-label="Donation pagination">
  <ul class="pagination justify-content-center mt-4">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php
        $urlParams = $_GET;
        $urlParams['page'] = $i;
        $queryString = http_build_query($urlParams);
      ?>
      <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
        <a class="page-link" href="?<?= $queryString ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

  <?php else: ?>
    <p class="text-muted">No donations found for this campaign.</p>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
