<?php
session_start();
require_once("../includes/db_connect.php");

// Only systemAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$adminName = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Admin Dashboard</title>
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/systemAdmindashboard.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include_once("../Templates/nav_systemAdmin.php"); ?>

<div class="container py-5">
  <h2 class="mb-4 text-primary text-center">👨‍💼 Welcome, <?= htmlspecialchars($adminName) ?>!</h2>

  <!-- Verification Documents -->
  <div class="mb-5">
    <h4 class="text-secondary">📄 Pending School Verification Documents</h4>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>School</th>
          <th>Document</th>
          <th>Uploaded</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $docQuery = "SELECT vd.id, vd.file_name, vd.upload_time, vd.status, u.school_name
                     FROM verification_documents vd
                     JOIN users u ON vd.schoolAdmin_id = u.user_id
                     WHERE vd.status = 'Pending'";
        $docResult = $conn->query($docQuery);
        if ($docResult->num_rows > 0):
          while ($doc = $docResult->fetch_assoc()):
        ?>
        <tr>
          <td><?= htmlspecialchars($doc['school_name']) ?></td>
          <td><a href="../uploads/<?= htmlspecialchars($doc['file_name']) ?>" target="_blank">View</a></td>
          <td><?= date('d M Y H:i', strtotime($doc['upload_time'])) ?></td>
          <td><span class="badge bg-warning text-dark"><?= $doc['status'] ?></span></td>
          <td>
            <a href="../Processes/verify_campaign.php?action=approve&id=<?= $row['campaign_id'] ?>" class="btn btn-success btn-sm">Approve</a>
            <a href="../Processes/reject_campaign.php?id=<?= $row['campaign_id'] ?>" class="btn btn-danger btn-sm">Reject</a>

          </td>
        </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="5" class="text-muted text-center">No pending documents</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Campaign Approval -->
  <div>
    <h4 class="text-secondary">🎯 Campaigns Awaiting Approval</h4>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Campaign</th>
          <th>School</th>
          <th>Target Amount</th>
          <th>Created</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $campQuery = "SELECT c.campaign_id, c.campaign_name, c.target_amount, c.created_at, c.status, u.school_name
                      FROM campaigns c
                      JOIN users u ON c.schoolAdmin_id = u.user_id
                      WHERE c.status = 'Pending'";
        $campResult = $conn->query($campQuery);
        if ($campResult->num_rows > 0):
          while ($camp = $campResult->fetch_assoc()):
        ?>
        <tr>
          <td><?= htmlspecialchars($camp['campaign_name']) ?></td>
          <td><?= htmlspecialchars($camp['school_name']) ?></td>
          <td>Ksh <?= number_format($camp['target_amount'], 2) ?></td>
          <td><?= date('d M Y', strtotime($camp['created_at'])) ?></td>
          <td><span class="badge bg-warning text-dark"><?= $camp['status'] ?></span></td>
          <td>
            <a href="../Processes/verify_campaign.php?id=<?= $camp['campaign_id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
            <a href="../Processes/verify_campaign.php?id=<?= $camp['campaign_id'] ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
          </td>
        </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-muted text-center">No campaigns pending approval</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
