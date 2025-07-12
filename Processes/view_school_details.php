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

// Get school details + verification status
$schoolQuery = "SELECT u.*, sp.school_name, 
                   (SELECT status FROM verification_documents 
                    WHERE schoolAdmin_id = u.user_id 
                    ORDER BY upload_time DESC 
                    LIMIT 1) AS verification_status
                FROM users u 
                JOIN school_profiles sp ON u.user_id = sp.schoolAdmin_id 
                WHERE u.user_id = $school_id AND u.role = 'schoolAdmin'";
$school = $conn->query($schoolQuery)->fetch_assoc();

// Get campaigns
$campaigns = $conn->query("SELECT * FROM campaigns WHERE schoolAdmin_id = $school_id");

// Get donations
$donations = $conn->query("SELECT d.amount, d.donation_date, c.campaign_name 
                           FROM donations d 
                           JOIN campaigns c ON d.campaign_id = c.campaign_id 
                           WHERE c.schoolAdmin_id = $school_id");

// Get verification documents
$documents = $conn->query("SELECT file_name, upload_time, status 
                           FROM verification_documents 
                           WHERE schoolAdmin_id = $school_id 
                           ORDER BY upload_time DESC");

// Get totals
$totals = $conn->query("SELECT 
                          SUM(c.target_amount) AS total_requested,
                          SUM(d.amount) AS total_donated
                        FROM campaigns c
                        LEFT JOIN donations d ON c.campaign_id = d.campaign_id
                        WHERE c.schoolAdmin_id = $school_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Details</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    .section-box {
      background-color: #eaf3fb; /* light blue */
      padding: 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }

    .btn-purple {
  background-color: #330066;
  color: #fff;
}

.btn-purple:hover {
  background-color: #290052;
}

  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-primary">🏫 School Details</h2>

  <!-- School Info -->
  <div class="section-box">
    <h4><?= htmlspecialchars($school['school_name']) ?></h4>
    <p>Email: <?= htmlspecialchars($school['email']) ?></p>
    <p>Verification Status: 
      <span class="badge bg-<?= 
        $school['verification_status'] === 'Approved' ? 'success' : 
        ($school['verification_status'] === 'Pending' ? 'warning text-dark' : 'danger') ?>">
        <?= $school['verification_status'] ?? 'Not Submitted' ?>
      </span>
    </p>
  </div>

  <!-- Totals Summary -->
  <div class="section-box">
    <h5 class="text-secondary">Summary</h5>
    <ul class="list-group">
      <li class="list-group-item">Total Campaign Amount Requested: <strong>Ksh <?= number_format($totals['total_requested'] ?? 0, 2) ?></strong></li>
      <li class="list-group-item">Total Donations Received: <strong>Ksh <?= number_format($totals['total_donated'] ?? 0, 2) ?></strong></li>
    </ul>
  </div>

  <!-- Documents -->
  <div class="section-box">
    <h5 class="text-secondary">Verification Documents</h5>
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr><th>Document</th><th>Uploaded</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php if ($documents->num_rows > 0): ?>
          <?php while ($doc = $documents->fetch_assoc()): ?>
          <tr>
            <td><a href="../uploads/<?= htmlspecialchars($doc['file_name']) ?>" target="_blank">View</a></td>
            <td><?= date('d M Y H:i', strtotime($doc['upload_time'])) ?></td>
            <td><?= $doc['status'] ?></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3" class="text-muted text-center">No documents uploaded</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Campaigns -->
  <div class="section-box">
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

  <!-- Donations -->
  <div class="section-box">
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
          <td><?= date('d M Y', strtotime($d['donation_date'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

 <a href="systemAdminDashboard.php" class="btn btn-purple mt-3">← Back to Dashboard</a>


</div>
</body>
</html>
