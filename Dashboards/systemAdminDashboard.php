<?php
session_start();
require_once("../includes/db_connect.php");

// Only systemAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$adminName = $_SESSION['username'];

// Fetch user, donor, and school data
$users = $conn->query("SELECT u.user_id, u.username, u.email, u.role, u.is_suspended AS suspended,
                             EXISTS (SELECT 1 FROM suspicious_activity WHERE user_id = u.user_id AND is_flagged = 1) AS flagged
                      FROM users u");

$donors = $conn->query("SELECT d.donor_Id, d.full_name, u.email,
                               COUNT(do.donation_Id) AS total_donations,
                               SUM(do.amount) AS total_amount
                        FROM donors d
                        LEFT JOIN users u ON d.user_id = u.user_id
                        LEFT JOIN donations do ON d.donor_Id = do.donor_Id AND do.status = 'Completed'
                        GROUP BY d.donor_Id, d.full_name, u.email");


$schools = $conn->query("SELECT u.user_id, sp.school_name, u.email, COUNT(c.campaign_id) AS total_campaigns,
                         SUM(c.target_amount) AS total_requested,
                         SUM(d.amount) AS total_received
                         FROM users u
                         JOIN school_profiles sp ON u.user_id = sp.schoolAdmin_id
                         LEFT JOIN campaigns c ON u.user_id = c.schoolAdmin_id
                         LEFT JOIN donations d ON c.campaign_id = d.campaign_id
                         WHERE u.role = 'schoolAdmin'
                         GROUP BY u.user_id, sp.school_name, u.email");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/adminfooter.css">
  <link rel="stylesheet" href="../CSS/dashboard.css"> <!-- Updated dashboard styles -->
</head>
<body>

<!-- Sidebar -->
<?php include_once("../Templates/sidebar.php"); ?>

<!-- Toggle button for mobile -->
<button class="toggle-sidebar" onclick="toggleSidebar()">
  <i class="fas fa-bars"></i>
</button>


<!-- Main Content -->
<div class="main-content">
  <h2 class="mb-4 fw-bold text-primary">👨‍💼 Welcome, <?= htmlspecialchars($adminName) ?>!</h2>

  <!-- Summary Widgets -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="summary-widget">
        <h6>Total Pending Documents</h6>
        <?php
        $docCount = $conn->query("SELECT COUNT(*) AS total FROM verification_documents WHERE status = 'Pending'");
        $docTotal = $docCount->fetch_assoc()['total'];
        ?>
        <h4><?= $docTotal ?></h4>
      </div>
    </div>
    <div class="col-md-6">
      <div class="summary-widget">
        <h6>Pending Campaigns</h6>
        <?php
        $campCount = $conn->query("SELECT COUNT(*) AS total FROM campaigns WHERE status = 'Pending'");
        $campTotal = $campCount->fetch_assoc()['total'];
        ?>
        <h4><?= $campTotal ?></h4>
      </div>
    </div>
  </div>

  <!-- Verification Documents Section -->
  <div class="section-block">
<h4 class="fw-bold mb-3" style="color: #003366;"><i class="fas fa-file-alt me-2"></i>Pending School Verification Documents</h4>
    <table class="table table-striped">
      <thead class="table-dark">
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
      $docQuery = "SELECT vd.id, vd.file_name, vd.upload_time, vd.status, sp.school_name
                   FROM verification_documents vd
                   JOIN users u ON vd.schoolAdmin_id = u.user_id
                   JOIN school_profiles sp ON sp.schoolAdmin_id = u.user_id
                   WHERE vd.status = 'Pending'";
      $docResult = $conn->query($docQuery);
      if ($docResult->num_rows > 0):
        while ($doc = $docResult->fetch_assoc()):
      ?>
        <tr>
          <td><?= htmlspecialchars($doc['school_name']) ?></td>
          <td><a href="../uploads/verificationdocs/<?= htmlspecialchars($doc['file_name']) ?>" target="_blank">View</a></td>
          <td><?= date('d M Y H:i', strtotime($doc['upload_time'])) ?></td>
          <td><span class="badge bg-warning text-dark"><?= $doc['status'] ?></span></td>
          <td>
            <a href="../Processes/verify_document.php?action=approve&id=<?= $doc['id'] ?>" class="btn btn-success btn-sm">Approve</a>
            <a href="../Processes/verify_document.php?action=reject&id=<?= $doc['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5" class="text-muted text-center">No pending documents</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Campaigns -->
  <div class="section-block">
    <h4 class="fw-bold mb-3" style="color: #003366;"><i class="fas fa-bullseye me-2"></i>Campaigns Awaiting Approval</h4>
    <table class="table table-striped">
      <thead class="table-dark">
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
      $campQuery = "SELECT c.campaign_id, c.campaign_name, c.target_amount, c.created_at, c.status, sp.school_name
                    FROM campaigns c
                    JOIN users u ON c.schoolAdmin_id = u.user_id
                    JOIN school_profiles sp ON u.user_id = sp.schoolAdmin_id
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
            <a href="../Processes/verify_campaign.php?action=approve&id=<?= $camp['campaign_id'] ?>" class="btn btn-success btn-sm">Approve</a>
            <a href="../Processes/rejectCampaign.php?id=<?= $camp['campaign_id'] ?>" class="btn btn-danger btn-sm">Reject</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="6" class="text-muted text-center">No campaigns pending approval</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- User Management -->
  <div class="section-block">
    <h4 class="fw-bold mb-3" style="color: #003366;"><i class="fas fa-users me-2"></i>User Management</h4>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($user = $users->fetch_assoc()):
        $status = $user['suspended'] ? 'Suspended' : ($user['flagged'] ? 'Flagged' : 'Active');
        $badgeClass = $user['suspended'] ? 'danger' : ($user['flagged'] ? 'warning' : 'success');
      ?>
        <tr class="<?= $user['suspended'] ? 'table-danger' : ($user['flagged'] ? 'table-warning' : '') ?>">
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td><span class="badge bg-<?= $badgeClass ?>"><?= $status ?></span></td>
          <td>
            <?php if (!$user['flagged']): ?>
              <a href="../Processes/flag_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning me-1">Flag</a>
    

            <?php else: ?>
  <a href="../Processes/unflag_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-success me-1">Unflag</a>
   <?php endif; ?>
            <?php if (!$user['suspended']): ?>
              <a href="../Processes/suspend_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-secondary me-1">Suspend</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Donor Records -->
  <div class="section-block">
    <h4 class="fw-bold mb-3" style="color: #003366;"><i class="fas fa-hand-holding-heart me-2"></i>Donor Records</h4>
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Total Donations</th>
          <th>Total Amount Donated</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($donor = $donors->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($donor['full_name']) ?></td>
          <td><?= htmlspecialchars($donor['email']) ?></td>
          <td><?= $donor['total_donations'] ?></td>
         <td>Ksh <?= number_format($donor['total_amount'] ?? 0, 2) ?></td>

          <td><a href="../Processes/view_donor_details.php?id=<?= $donor['donor_Id'] ?>" class="btn btn-sm btn-info">View</a></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- School Records -->
  <div class="section-block">
   <h4 class="fw-bold mb-3" style="color: #003366;"><i class="fas fa-school me-2"></i>School Records</h4>
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>School Name</th>
          <th>Email</th>
          <th>Total Campaigns</th>
          <th>Total Requested</th>
          <th>Total Donations Received</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($school = $schools->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($school['school_name']) ?></td>
          <td><?= htmlspecialchars($school['email']) ?></td>
          <td><?= $school['total_campaigns'] ?></td>
          <td>Ksh <?= number_format($school['total_requested'], 2) ?></td>
          <td>Ksh <?= number_format($school['total_received'], 2) ?></td>
          <td><a href="../Processes/view_school_details.php?id=<?= $school['user_id'] ?>" class="btn btn-sm btn-info">View</a></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once("../Templates/Admindashboard.php"); ?>

</body>
</html>

