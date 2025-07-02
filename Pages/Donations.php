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

// Collect filters from GET
$filters = [
    'date' => $_GET['date'] ?? '',
    'campaign' => $_GET['campaign'] ?? '',
    'amount' => $_GET['amount'] ?? '',
    'search' => $_GET['search'] ?? ''
];

// Helper for binding by reference
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

// Build WHERE clause dynamically
$whereClauses = ["d.donor_id = ?"];
$types = "i";
$values = [$donorId];

if (!empty($filters['date'])) {
    $whereClauses[] = "DATE(d.donation_date) = ?";
    $types .= "s";
    $values[] = $filters['date'];
}

if (!empty($filters['campaign'])) {
    $whereClauses[] = "c.campaign_name LIKE ?";
    $types .= "s";
    $values[] = "%" . $filters['campaign'] . "%";
}

if (!empty($filters['amount'])) {
    $whereClauses[] = "d.amount = ?";
    $types .= "d";
    $values[] = (float)$filters['amount'];
}

if (!empty($filters['search'])) {
    $whereClauses[] = "(c.campaign_name LIKE ? OR d.payment_mode LIKE ? OR d.status LIKE ?)";
    $types .= "sss";
    $values[] = "%" . $filters['search'] . "%";
    $values[] = "%" . $filters['search'] . "%";
    $values[] = "%" . $filters['search'] . "%";
}

$whereSQL = implode(" AND ", $whereClauses);

// Count total records
$countQuery = "SELECT COUNT(*) AS total 
               FROM donations d
               JOIN campaigns c ON d.campaign_id = c.campaign_id
               WHERE $whereSQL";

$countStmt = $conn->prepare($countQuery);
$params = array_merge([$types], $values);
call_user_func_array([$countStmt, 'bind_param'], refValues($params));
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalRecords = $countResult['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$countStmt->close();

// Fetch paginated data
$dataQuery = "SELECT d.donation_date, c.campaign_name, d.amount, d.payment_mode, d.status 
              FROM donations d
              JOIN campaigns c ON d.campaign_id = c.campaign_id
              WHERE $whereSQL
              ORDER BY d.donation_date DESC
              LIMIT ?, ?";

// Add pagination values
$types .= "ii";
$values[] = $startFrom;
$values[] = $recordsPerPage;

$dataStmt = $conn->prepare($dataQuery);
$params = array_merge([$types], $values);
call_user_func_array([$dataStmt, 'bind_param'], refValues($params));
$dataStmt->execute();
$result = $dataStmt->get_result();
$donations = $result->fetch_all(MYSQLI_ASSOC);
$dataStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate to Campaign</title>
    <link rel="stylesheet" href="../CSS/donations.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="donation-page">
   <!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>
<div class="container mt-5">
    <h2 class="mb-4">Make a Donation</h2>
    <form action="process_donation.php" method="POST">

        <input type="hidden" name="campaign_id" value="<?= htmlspecialchars($_GET['campaign_id'] ?? '') ?>">

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount (KES)</label>
            <input type="number" name="amount" class="form-control" required min="10">
        </div>

        <div class="mb-3">
            <label for="payment_mode" class="form-label">Payment Mode</label>
            <select name="payment_mode" class="form-select" required>
                <option value="M-Pesa">M-Pesa</option>
                <option value="Bank">Bank</option>
                <option value="Card">Card</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Donate Now</button>
    </form>
</div>

 <?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
