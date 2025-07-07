<?php
session_start();
require_once("../includes/db_connect.php");

// Only allow logged-in schoolAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];

// Fetch campaigns created by this schoolAdmin
$query = "SELECT * FROM campaigns WHERE schoolAdmin_id = ? ORDER BY start_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $schoolAdminId);
$stmt->execute();
$result = $stmt->get_result();
$campaigns = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Campaigns</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container py-5">
  <div class="bg-white p-4 rounded shadow">
    <h2 class="text-white bg-dark px-3 py-2 rounded" style="background-color: #003366 !important;">📋 Your Campaigns</h2>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
      <div class="alert alert-success text-center mt-3">✅ Campaign deleted successfully.</div>
    <?php endif; ?>

    <?php if (empty($campaigns)): ?>
      <p class="text-muted mt-4">You haven’t created any campaigns yet.</p>
    <?php else: ?>
      <div class="row mt-4">
        <?php foreach ($campaigns as $campaign): ?>
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="../uploads/<?= htmlspecialchars($campaign['image_path']) ?>" class="card-img-top" alt="Campaign Image">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($campaign['campaign_name']) ?></h5>
                <p class="card-text small">Category: <?= htmlspecialchars($campaign['category']) ?></p>
                <p class="card-text">Target: KES <?= number_format($campaign['target_amount']) ?></p>
                <p class="card-text"><small>Status: <strong><?= $campaign['status'] ?></strong></small></p>
                <div class="d-flex justify-content-between">
                  <a href="../Pages/editCampaign.php?id=<?= $campaign['campaign_id'] ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <a href="../Processes/deleteCampaign.php?campaign_id=<?= $campaign['campaign_id'] ?>"
                     onclick="return confirm('Are you sure you want to delete this campaign?');"
                     class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i> Delete
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
