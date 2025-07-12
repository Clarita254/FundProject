<?php
$role = $_SESSION['role'] ?? '';
?>
<div class="sidebar" id="sidebar">
  <a href="#" class="fw-bold fs-5 text-white px-4 mb-3 d-block">EduFund <?= ucfirst($role) ?></a>

  <?php if ($role === 'donor'): ?>
    <a href="../Pages/Campaign.php"><i class="fas fa-search me-2"></i> Search Campaigns</a>
    <a href="../Pages/Donationhistory.php"><i class="fas fa-clock me-2"></i> Donation History</a>
    <a href="../Pages/Leaderboard.php"><i class="fas fa-trophy me-2"></i> Leaderboard</a>
    <a href="../Pages/donorReports.php"><i class="fas fa-chart-line me-2"></i> Fund Reports</a>
    <a href="../mpesa/myreceipts.php"><i class="fas fa-file-invoice me-2"></i> Receipt</a>

  <?php elseif ($role === 'schoolAdmin'): ?>
    <a href="../Dashboards/schoolAdmindashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
    <a href="../Pages/createCampaign.php"><i class="fas fa-plus me-2"></i> Create Campaign</a>
    <a href="../Dashboards/manageCampaigns.php"><i class="fas fa-tasks me-2"></i> My Campaigns</a>
    <a href="../Pages/progressReports.php"><i class="fas fa-file-alt me-2"></i> Progress Reports</a>
    <a href="../Pages/submitProgressReport.php"><i class="fas fa-upload me-2"></i> Submit Report</a>

  <?php elseif ($role === 'systemAdmin'): ?>
    <a href="../Dashboards/systemAdminDashboard.php"><i class="fas fa-user-shield me-2"></i> Admin Home</a>
    <a href=" "><i class="fas fa-check-circle me-2"></i> Verify School documents </a>
    <a href=" "><i class="fas fa-check-double me-2"></i> Approve/reject Campaigns</a>
    <a href=" "><i class="fas fa-users-cog me-2"></i>User Management</a>
    <a href=" "><i class="fas fa-users-cog me-2"></i>Donor Records</a>
    <a href=" "><i class="fas fa-users-cog me-2"></i>School Records</a>
  <?php endif; ?>

  <a href="../includes/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>
