<?php
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$campaignId = $_GET['donation_Id'] ?? null;
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome (optional) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #003366;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #f4f8fb;
    }

    .edit-campaign-container {
      background-color: #ffffff;
      border-radius: 16px;
      padding: 40px;
      max-width: 700px;
      margin: 60px auto;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .edit-title {
      color: #003366;
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
    }

    .form-label {
      color: #003366;
      font-weight: 600;
    }

    .btn-update {
      background-color: #003366;
      color: white;
      padding: 10px 25px;
      font-weight: 500;
      border-radius: 8px;
    }

    .btn-update:hover {
      background-color: #002752;
    }
  </style>
</head>

<body>

<div class="edit-campaign-container">
  <h2 class="edit-title">✏️ Edit Campaign</h2>

  <form action="../Processes/updateCampaign.php" method="POST">
    <input type="hidden" name="campaign_id" value="<?= $campaign['campaign_id'] ?>">

    <div class="mb-3">
      <label for="campaignTitle" class="form-label">Campaign Title</label>
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

    <div class="text-end mt-4">
      <button type="submit" class="btn btn-update">
        <i class="fas fa-save me-1"></i> Update Campaign
      </button>
    </div>
  </form>
</div>

</body>
</html>
