<?php
session_start();
require_once("../includes/db_connect.php");

// Only schoolAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolName = $_SESSION['username'] ?? 'School Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Admin Dashboard</title>
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/schoolAdmindashboard.css">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  
</head>
<body>

<?php include_once("../Templates/navschoolAdmin.php"); ?>

<div class="container py-5" style="background: linear-gradient(to bottom right, #eaf6f9, #f5fafd); border-radius: 15px; padding: 40px;">

  <h2 class="text-center fw-bold mb-4" style="color: #003c58;; font-size: 2.2rem;">
  👋 Welcome Back, <?= htmlspecialchars($schoolName) ?>!
</h2>

  <!-- Quick Actions -->
  <div class="row text-center mb-5">
    <div class="col-md-4 mb-3">
      <a href="../Pages/Campaigncreation.php" class="btn btn-outline-success w-100">
        <i class="fas fa-plus-circle me-2"></i>Create Campaign
      </a>
    </div>
    <div class="col-md-4 mb-3">
      <a href="../Pages/Campaign.php" class="btn btn-outline-info w-100">
        <i class="fas fa-edit me-2"></i>View  Campaigns
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <a href="../Dashboards/manageCampaigns.php" class="btn btn-outline-info w-100">
        <i class="fas fa-edit me-2"></i>Manage Campaign
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <a href="../Pages/ProgressForm.php" class="btn btn-outline-warning w-100">
        <i class="fas fa-chart-line me-2"></i>Submit Fund Utilization Report
      </a>
    </div>
  </div>

  <!-- Upload Verification Documents -->
  <div class="dashboard-card mb-4">
    <h5 class="section-title">School Verification Documents</h5>
    <form action="../uploads/uploadDocuments.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="verificationDoc" class="form-label">Upload Verification Document (PDF, Image i.e school identity):</label>
        <input type="file" name="verificationDoc" class="form-control" required>
      </div>
      <button type="submit" class="btn "style="background-color:#145c72; color: white;">Upload</button>
    </form>
  </div>

   <!-- Display uploaded document status -->
<div class="dashboard-card mb-4">
  <h5 class="section-title">Verification Document Status</h5>

  <?php
  $stmt = $conn->prepare("SELECT file_name, upload_time, status FROM verification_documents WHERE schoolAdmin_id = ? ORDER BY upload_time DESC LIMIT 1");
  $stmt->bind_param("i", $schoolAdminId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0):
    $doc = $result->fetch_assoc();
  ?>
    <ul class="list-group">
      <li class="list-group-item"><strong>File:</strong> <?= htmlspecialchars($doc['file_name']) ?></li>
      <li class="list-group-item"><strong>Status:</strong> 
        <span class="badge 
          <?= $doc['status'] === 'Approved' ? 'bg-success' : ($doc['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
          <?= htmlspecialchars($doc['status']) ?>
        </span>
      </li>
      <li class="list-group-item"><strong>Uploaded:</strong> <?= date('d M Y H:i', strtotime($doc['upload_time'])) ?></li>
    </ul>
  <?php else: ?>
    <p class="text-muted">No verification document uploaded yet.</p>
  <?php endif; $stmt->close(); ?>
</div>


    </form>

    <!-- Campaign Approval Status -->
  <div class="dashboard-card">
    <h5 class="section-title">Your Campaigns and Approval Status</h5>
    <?php
      $schoolAdminId = $_SESSION['user_id'];
      $statusQuery = "SELECT campaign_name, status, created_at FROM campaigns WHERE schoolAdmin_id = ?";
      $stmt = $conn->prepare($statusQuery);
      $stmt->bind_param("i", $schoolAdminId);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0):
    ?>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Campaign Title</th>
          <th>Status</th>
          <th>Date Created</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['campaign_name']) ?></td>
          <td><span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p class="text-muted mt-3">No campaigns found yet.</p>
    <?php endif; ?>
    <?php $stmt->close(); ?>
  </div>

  </div>

  
  <!-- Logout -->
  <div class="text-center mt-4">
    <a href="../includes/logout.php" class="btn "style="background-color:#145c72; color: white;">
      <i class="fas fa-sign-out-alt me-2"></i>Logout
    </a>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
