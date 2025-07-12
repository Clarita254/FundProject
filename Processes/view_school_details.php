<?php
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$school_id = intval($_GET['id']);
$school = $conn->query("SELECT * FROM users WHERE user_id = $school_id AND role = 'schoolAdmin'")->fetch_assoc();

$campaigns = $conn->query("SELECT * FROM campaigns WHERE schoolAdmin_id = $school_id");
$donations = $conn->query("SELECT d.amount, d.date, c.campaign_name 
                           FROM donations d 
                           JOIN campaigns c ON d.campaign_id = c.campaign_id 
                           WHERE c.schoolAdmin_id = $school_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Details</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-primary">🏫 School Details</h2>
  <div class="bg-white p-4 rounded shadow-sm">
    <h4><?= htmlspecialchars($school['school_name']) ?></h4>
    <p>Email: <?= htmlspecialchars($school['email']) ?></p>
  </div>

  <div class="bg-white p-4 rounded shadow-sm mt-4">
    <h5 class="text-secondary">Campaigns Created</h5>
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr><th>Name</th><th>Target</th><th>Status</th><th>Created</th></tr>
      </thead>
      <tbody>
        <?php while ($c = $campaigns->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($c['campaign_name']) ?></td>
          <td>Ksh <?= number_format($c['target_amount'], 2) ?></td>
          <td><?= $c['status'] ?></td>
          <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="bg-white p-4 rounded shadow-sm mt-4">
    <h5 class="text-secondary">Donations Received</h5>
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr><th>Campaign</th><th>Amount</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php while ($d = $donations->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($d['campaign_name']) ?></td>
          <td>Ksh <?= number_format($d['amount'], 2) ?></td>
          <td><?= date('d M Y', strtotime($d['date'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <a href="systemAdminDashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
</body>
</html>
