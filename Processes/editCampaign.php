<?php
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$campaignId = $_GET['id'] ?? null;
$schoolAdminId = $_SESSION['user_id'];

if (!$campaignId) {
    die("Invalid campaign ID.");
}

$query = "SELECT * FROM campaigns WHERE campaign_id = ? AND schoolAdmin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $campaignId, $schoolAdminId);
$stmt->execute();
$result = $stmt->get_result();
$campaign = $result->fetch_assoc();
$stmt->close();

if (!$campaign) {
    die("Campaign not found or you don't have permission to edit.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Campaign</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="text-primary text-center mb-4">Edit Campaign</h2>
  <form action="updateCampaign.php" method="POST">
    <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id'] ?>">

    <div class="mb-3">
      <label for="campaignTitle" class="form-label">Title</label>
      <input type="text" class="form-control" id="campaignTitle" name="campaignTitle" value="<?= htmlspecialchars($campaign['campaign_name']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="campaignDescription" class="form-label">Description</label>
      <textarea class="form-control" name="campaignDescription" rows="4" required><?= htmlspecialchars($campaign['description']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="targetAmount" class="form-label">Target Amount (KES)</label>
      <input type="number" class="form-control" name="targetAmount" value="<?= htmlspecialchars($campaign['target_amount']) ?>" min="100" required>
    </div>

    <div class="mb-3">
      <label for="endDate" class="form-label">End Date</label>
      <input type="date" class="form-control" name="endDate" value="<?= htmlspecialchars($campaign['end_date']) ?>" required>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-success">Update Campaign</button>
    </div>
  </form>
</div>
</body>
</html>
