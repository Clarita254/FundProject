<?php
require_once('../includes/db_connect.php');
session_start();

// Ensure only donors can view this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
    

}

$donorId = $_SESSION['user_id'];
$donations = [];
$totalPages = 0;
$recordsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startFrom = ($currentPage - 1) * $recordsPerPage;

//  Initialize filters from GET -regular http GET
$filters = [
    'date' => $_GET['date'] ?? '',
    'campaign' => $_GET['campaign'] ?? '',
    'amount' => $_GET['amount'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Base query
$whereClauses = ["d.donor_id = ?"];
$params = ["i", $donorId]; // bind types: i = integer, s = string

// Add individual filters dynamically
if (!empty($filters['date'])) {
    $whereClauses[] = "DATE(d.donation_date) = ?";
    $params[0] .= "s";
    $params[] = $filters['date'];
}

if (!empty($filters['campaign'])) {
    $whereClauses[] = "c.campaign_name LIKE ?";
    $params[0] .= "s";
    $params[] = "%" . $filters['campaign'] . "%";
}

if (!empty($filters['amount'])) {
    $whereClauses[] = "d.amount = ?";
    $params[0] .= "d";
    $params[] = (float)$filters['amount'];
}

if (!empty($filters['search'])) {
    $whereClauses[] = "(c.campaign_name LIKE ? OR d.payment_mode LIKE ? OR d.status LIKE ?)";
    $params[0] .= "sss";
    $params[] = "%{$filters['search']}%";
    $params[] = "%{$filters['search']}%";
    $params[] = "%{$filters['search']}%";
}

$whereSQL = implode(" AND ", $whereClauses);

// Count total records
$countQuery = "SELECT COUNT(*) AS total 
               FROM donations d
               JOIN campaigns c ON d.campaign_id = c.campaign_id
               WHERE $whereSQL";

$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param(...$params);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalRecords = $countResult['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$countStmt->close();

// Fetch filtered donations with LIMIT
$dataQuery = "SELECT d.donation_date, c.campaign_name, d.amount, d.payment_mode, d.status 
              FROM donations d
              JOIN campaigns c ON d.campaign_id = c.campaign_id
              WHERE $whereSQL
              ORDER BY d.donation_date DESC
              LIMIT ?, ?";

// Add pagination to binding
$params[0] .= "ii";
$params[] = $startFrom;
$params[] = $recordsPerPage;

$dataStmt = $conn->prepare($dataQuery);
$dataStmt->bind_param(...$params);
$dataStmt->execute();
$result = $dataStmt->get_result();
$donations = $result->fetch_all(MYSQLI_ASSOC);
$dataStmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donation History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/Donationhistory.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <?php include_once("../Templates/nav.php"); ?>
</head>
<body>
<div class="container py-5">
  <h2 class="text-center mb-4 fw-bold">DONATION HISTORY</h2>

  <!-- Filters -->
  <form class="filters d-flex flex-wrap gap-3 justify-content-center mb-4" method="GET">
    <input type="text" name="date" class="form-control" placeholder="Search by Date" value="<?= htmlspecialchars($filters['date']) ?>" style="max-width: 160px;">
    <input type="text" name="campaign" class="form-control" placeholder="Filter by Campaign" value="<?= htmlspecialchars($filters['campaign']) ?>" style="max-width: 160px;">
    <input type="text" name="amount" class="form-control" placeholder=" Serach by Amount" value="<?= htmlspecialchars($filters['amount']) ?>" style="max-width: 160px;">
    <div class="input-group" style="max-width: 200px;">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" name="search" class="form-control" placeholder="Search all Fields" value="<?= htmlspecialchars($filters['search']) ?>">
    </div>
    <button type="submit" class="btn btn-success">Search</button>
  </form>

  <!-- Table -->
  <div class="table-responsive card-table">
    <table class="table table-bordered text-center">
      <thead>
        <tr>
          <th>Date</th>
          <th>Campaign</th>
          <th>Amount Donated</th>
          <th>Payment Mode</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($donations) === 0): ?>
          <tr><td colspan="5" class="text-center">No donation records found.</td></tr>
        <?php else: ?>
          <?php foreach ($donations as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['donation_date']) ?></td>
              <td><?= htmlspecialchars($d['campaign_name']) ?></td>
              <td><?= 'KES ' . number_format($d['amount'], 2) ?></td>
              <td><?= htmlspecialchars($d['payment_mode']) ?></td>
              <td><?= htmlspecialchars($d['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <nav class="mt-4 d-flex justify-content-center">
      <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
