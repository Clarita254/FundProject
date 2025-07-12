<?php
session_start();
require_once("../includes/db_connect.php");

// Only schoolAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];
$schoolName = $_SESSION['username'] ?? 'School Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Admin Dashboard</title>
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/Progressform.css">
  <link rel="stylesheet" href="../CSS/schoolAdmindashboard.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar toggle -->
<button class="toggle-sidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <a href="../Dashboards/schoolAdmindashboard.php" class="active"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="../Pages/Campaigncreation.php"><i class="fas fa-plus-circle me-2"></i> Create Campaign</a>
  <a href="../Pages/Campaign.php"><i class="fas fa-eye me-2"></i> View Campaigns</a>
  <a href="../Dashboards/manageCampaigns.php"><i class="fas fa-tasks me-2"></i> Manage Campaigns</a>
  <a href="../Processes/Progressform.php"><i class="fas fa-file-alt me-2"></i> Fund Utilization Form</a>
  <a href="../Pages/ProgressReport.php"><i class="fas fa-chart-line me-2"></i> Fund Utilization Reports</a>
  <a href="../includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <h2 class="fw-bold mb-4 text-primary text-center">Welcome, <?= htmlspecialchars($schoolName) ?>!</h2>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="summary-widget">
        <h6>Verification Docs</h6>
        <h4><?php
          $count = $conn->query("SELECT COUNT(*) as total FROM verification_documents WHERE schoolAdmin_id = $schoolAdminId")->fetch_assoc();
          echo $count['total'];
        ?></h4>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-widget">
        <h6>Campaigns Created</h6>
        <h4><?php
          $count = $conn->query("SELECT COUNT(*) as total FROM campaigns WHERE schoolAdmin_id = $schoolAdminId")->fetch_assoc();
          echo $count['total'];
        ?></h4>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-widget">
        <h6>Reports Submitted</h6>
        <h4><?php
          $count = $conn->query("SELECT COUNT(*) as total FROM progress_reports WHERE schoolAdmin_id = $schoolAdminId")->fetch_assoc();
          echo $count['total'] ?? 0;
        ?></h4>
      </div>
    </div>
  </div>

  <!-- Upload Verification Document -->
  <div class="dashboard-card mt-4">
    <h5 class="mb-3"><i class="fas fa-upload me-2"></i> Upload Verification Document</h5>
    <form action="../uploads/uploadDocuments.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <input type="file" name="verification_document" class="form-control" required>
        <input type="hidden" name="schoolAdmin_id" value="<?= $schoolAdminId ?>">
      </div>
      <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload</button>
    </form>
  </div>

  <!-- Latest Verification Document -->
  <div class="dashboard-card mt-4">
    <h5 class="mb-3"><i class="fas fa-id-card-alt me-2"></i> Latest Verification Document</h5>
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
        <span class="badge <?= $doc['status'] === 'Approved' ? 'bg-success' : ($doc['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
          <?= htmlspecialchars($doc['status']) ?>
        </span>
      </li>
      <li class="list-group-item"><strong>Uploaded:</strong> <?= date('d M Y H:i', strtotime($doc['upload_time'])) ?></li>
    </ul>
    <?php else: ?>
      <p class="text-muted">No document uploaded yet.</p>
    <?php endif; ?>
  </div>

  <!-- Campaign Status -->
  <div class="dashboard-card mt-4">
    <h5 class="mb-3"><i class="fas fa-bullhorn me-2"></i> Campaign Status</h5>
    <?php
    $stmt = $conn->prepare("SELECT campaign_name, status, created_at FROM campaigns WHERE schoolAdmin_id = ?");
    $stmt->bind_param("i", $schoolAdminId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0):
    ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Campaign</th>
          <th>Status</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()):
          $status = $row['status'];
          $badgeClass = match ($status) {
            'Approved' => 'bg-success',
            'Rejected' => 'bg-danger',
            'Pending' => 'bg-warning text-dark',
            default => 'bg-secondary'
          };
        ?>
        <tr>
          <td><?= htmlspecialchars($row['campaign_name']) ?></td>
          <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
          <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p class="text-muted">No campaigns yet.</p>
    <?php endif; ?>
  </div>

  <!-- Logout -->
  <div class="text-center mt-4">
    <a href="../includes/logout.php" class="btn btn-danger">
      <i class="fas fa-sign-out-alt me-2"></i> Logout
    </a>
  </div>
</div>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
  }
</script>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
