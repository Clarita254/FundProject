<?php
 //require_once('../includes/db_connect.php');

//Safe fallback values
$donations = [];
$totalPages = 0;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;


// Pagination setup
$recordsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startFrom = ($currentPage - 1) * $recordsPerPage;


// Filter inputs
$filters = [
  'date' => $_GET['date'] ?? '',
  'campaign' => $_GET['campaign'] ?? '',
  'amount' => $_GET['amount'] ?? '',
  'search' => $_GET['search'] ?? ''
];

// Base query
$query = "SELECT * FROM donations WHERE 1";
$params = [];

if (!empty($filters['date'])) {
  $query .= " AND date LIKE ?";
  $params[] = "%{$filters['date']}%";
}

if (!empty($filters['campaign'])) {
  $query .= " AND campaign LIKE ?";
  $params[] = "%{$filters['campaign']}%";
}

if (!empty($filters['amount'])) {
  $query .= " AND amount LIKE ?";
  $params[] = "%{$filters['amount']}%";
}

if (!empty($filters['search'])) {
  $query .= " AND (campaign LIKE ? OR mode LIKE ? OR status LIKE ?)";
  $params[] = "%{$filters['search']}%";
  $params[] = "%{$filters['search']}%";
  $params[] = "%{$filters['search']}%";
}

/*
$totalQuery = $conn->prepare($query);
$totalQuery->execute($params);
$totalRecords = $totalQuery->rowCount();
$totalPages = ceil($totalRecords / $recordsPerPage);

$query .= " ORDER BY date DESC LIMIT $startFrom, $recordsPerPage";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <title>Donation History</title>
 <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../CSS/footer.css">


  <?php include_once("../Templates/nav.php"); ?>
</head>
<body>
<div class="container py-5">
  <h2 class="text-center mb-4 fw-bold">DONATION HISTORY</h2>

  <!-- Filters -->
  <form class="filters d-flex flex-wrap gap-3 justify-content-center mb-4" method="GET">
    <input type="text" name="date" class="form-control" placeholder="Date" value="<?= htmlspecialchars($filters['date']) ?>" style="max-width: 160px;">
    <input type="text" name="campaign" class="form-control" placeholder="Campaign" value="<?= htmlspecialchars($filters['campaign']) ?>" style="max-width: 160px;">
    <input type="text" name="amount" class="form-control" placeholder="Amount" value="<?= htmlspecialchars($filters['amount']) ?>" style="max-width: 160px;">
    <div class="input-group" style="max-width: 200px;">
      <span class="input-group-text"><i class="fas fa-search"></i></span>
      <input type="text" name="search" class="form-control" placeholder="Search" value="<?= htmlspecialchars($filters['search']) ?>">
    </div>
    <button type="submit" class="btn btn-success">Filter</button>
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
          <tr><td colspan="5" class="no-records">No donation records found.</td></tr>
        <?php else: ?>
          <?php foreach ($donations as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['date']) ?></td>
              <td><?= htmlspecialchars($d['campaign']) ?></td>
              <td><?= htmlspecialchars($d['amount']) ?></td>
              <td><?= htmlspecialchars($d['mode']) ?></td>
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
            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>

