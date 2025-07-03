<?php  // Query donations table and show the most recent donation
session_start();
require_once('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$donorId = $_SESSION['user_id'];

// Get latest donation for donor
$stmt = $conn->prepare("SELECT d.*, c.campaign_name 
                        FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donor_id = ?
                        ORDER BY d.donation_date DESC
                        LIMIT 1");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Status</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4 text-center">Donation Status</h2>

  <?php if ($donation): ?>
    <div class="card p-4 shadow-sm">
      <p><strong>Campaign:</strong> <?= htmlspecialchars($donation['campaign_name']) ?></p>
      <p><strong>Amount:</strong> KES <?= number_format($donation['amount'], 2) ?></p>
      <p><strong>Status:</strong> 
        <?php
        if ($donation['status'] === 'Completed') {
            echo '<span class="text-success fw-bold">Completed</span>';
        } elseif ($donation['status'] === 'Failed') {
            echo '<span class="text-danger fw-bold">Failed</span>';
        } else {
            echo '<span class="text-warning fw-bold">Pending</span>';
        }
        ?>
      </p>

      <?php if ($donation['status'] === 'Completed'): ?>
        <a href="receipt.php?donation_id=<?= $donation['donation_id'] ?>" class="btn btn-primary mt-3">View Receipt</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">You haven’t made any donations yet.</div>
  <?php endif; ?>
</div>
</body>
</html>
