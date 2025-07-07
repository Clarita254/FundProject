<?php
session_start();
require_once("../includes/db_connect.php");

// Access control: only schoolAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];
$campaigns = []; // Always define the variable

// Fetch campaigns created by this school admin
$query = "SELECT campaign_id, campaign_name, category, target_amount, status, review_comment 
          FROM campaigns WHERE schoolAdmin_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("i", $schoolAdminId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $campaigns = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Campaigns - EduFund</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
<link rel="stylesheet" href="../CSS/manageCampaigns.css">
  
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="container py-5">
  <h2 class="section-title text-center mb-4"><i class="fas fa-clipboard-list me-2"></i> My Campaigns</h2>

  <?php if (count($campaigns) > 0): ?>
    <div class="row">
      <?php foreach ($campaigns as $campaign): ?>
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($campaign['campaign_name']) ?></h5>
              <p class="card-text">Category: <?= htmlspecialchars($campaign['category']) ?></p>
              <p class="card-text">Target: KES <?= number_format($campaign['target_amount']) ?></p>
              <p class="card-text">
                Status:
                <span class="badge 
                  <?= $campaign['status'] === 'Approved' ? 'bg-success' : 
                      ($campaign['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                  <?= htmlspecialchars($campaign['status']) ?>
                </span>
              </p>

              <?php if ($campaign['status'] === 'Rejected' && !empty($campaign['review_comment'])): ?>
                <div class="alert alert-warning p-2">
                  <strong>Feedback:</strong> <?= htmlspecialchars($campaign['review_comment']) ?>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-between mt-3">
                <a href="../Processes/editCampaign.php?donation_Id=<?= $campaign['campaign_id'] ?>" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <a href="../uploads/uploadDocuments.php?donation_Id=<?= $campaign['campaign_id'] ?>" class="btn btn-outline-info btn-sm">
                  <i class="fas fa-upload"></i> Upload Document
                </a>
                <a href="../Processes/viewdonations.php?donation_Id=<?= $campaign['campaign_id'] ?>" class="btn btn-outline-success btn-sm">
                  <i class="fas fa-donate"></i> Donations
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted text-center">You haven't created any campaigns yet.</p>
  <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
