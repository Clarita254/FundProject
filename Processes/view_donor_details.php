
<?php

//Added error handling when the user is not found
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$donor_id = intval($_GET['id']);

// Get donor details
$donor_result = $conn->query("SELECT * FROM donors WHERE donor_Id = $donor_id");
if (!$donor_result || $donor_result->num_rows === 0) {
    echo "<h2 style='color:white; text-align:center; padding-top:3rem;'>❌ Donor not found.</h2>";
    exit();
}
$donor = $donor_result->fetch_assoc();

// Get donation history
$donations = $conn->query("SELECT d.amount, d.donation_date, c.campaign_name 
                           FROM donations d 
                           JOIN campaigns c ON d.campaign_id = c.campaign_id 
                           WHERE d.donor_Id = $donor_id");
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donor Details | EduFund</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  

  <style>
    body {
      background: linear-gradient(to right, #001f3f, #003366);
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card-custom {
      background-color: #f8f9fa;
      color: #003366;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .card-custom h4 {
      color: #001f3f;
      font-weight: 700;
    }

    .btn-secondary {
      background-color: #003366;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #001f3f;
    }

    .table thead {
      background-color: #003366;
      color: #fff;
    }

    .table tbody tr:hover {
      background-color: #e9f2fb;
    }

    .text-primary {
      color: #7fdbff !important;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <h2 class="mb-4 text-primary">🎁 Donor Details</h2>

  <div class="card card-custom p-4 mb-4">
    <h4><?= htmlspecialchars($donor['full_name']) ?></h4>
    <p>Donor Number: <strong><?= htmlspecialchars($donor['username']) ?></strong></p>
  </div>

  <div class="card card-custom p-4">
    <h5 class="text-secondary mb-3">Donation History</h5>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Campaign</th>
          <th>Amount</th>
          <th>Date</th>
        </tr>
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

  <a href="../Dashboards/systemAdminDashboard.php" class="btn btn-secondary mt-4">← Back to Dashboard</a>
</div>
</body>


</html>



