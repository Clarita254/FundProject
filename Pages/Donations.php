<?php
require_once("../includes/db_connect.php");
session_start();

// Redirect if not logged in as donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Check for campaign ID
if (!isset($_GET['campaign_id'])) {
    header("Location: ../Pages/Campaign.php");
    exit();
}

$campaignId = intval($_GET['campaign_id']);
$donorId = $_SESSION['user_id'];

// Fetch campaign details
$campaignQuery = "SELECT * FROM campaigns WHERE campaign_id = ?";
$stmt = $conn->prepare($campaignQuery);
$stmt->bind_param("i", $campaignId);
$stmt->execute();
$campaignResult = $stmt->get_result();
$campaign = $campaignResult->fetch_assoc();
$stmt->close();

if (!$campaign) {
    echo "<div class='alert alert-danger'>Campaign not found.</div>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $paymentMode = $_POST['payment_mode'];

    $insertQuery = "INSERT INTO donations (donor_id, campaign_id, amount, payment_mode, status) VALUES (?, ?, ?, ?, 'Pending')";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iids", $donorId, $campaignId, $amount, $paymentMode);

    if ($insertStmt->execute()) {
        $successMsg = "Thank you for your donation of KES " . number_format($amount, 2);
    } else {
        $errorMsg = "Donation failed. Please try again.";
    }
    $insertStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donate to <?= htmlspecialchars($campaign['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
  <h2 class="mb-4">Donate to: <?= htmlspecialchars($campaign['title']) ?></h2>
  <?php if (isset($successMsg)): ?>
    <div class="alert alert-success"> <?= $successMsg ?> </div>
  <?php elseif (isset($errorMsg)): ?>
    <div class="alert alert-danger"> <?= $errorMsg ?> </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-body">
      <h5><?= htmlspecialchars($campaign['title']) ?></h5>
      <p><?= htmlspecialchars($campaign['description']) ?></p>
      <p><strong>Target:</strong> KES <?= number_format($campaign['target_amount']) ?></p>
      <p><strong>Deadline:</strong> <?= htmlspecialchars($campaign['end_date']) ?></p>
    </div>
  </div>

  <form method="POST" class="border p-4 rounded bg-light">
    <div class="mb-3">
      <label for="amount" class="form-label">Donation Amount (KES)</label>
      <input type="number" step="10" class="form-control" id="amount" name="amount" required>
    </div>

    <div class="mb-3">
      <label for="payment_mode" class="form-label">Payment Mode</label>
      <select class="form-select" id="payment_mode" name="payment_mode" required>
        <option value="Mpesa">Mpesa</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Card">Card</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Donate Now</button>
    <a href="Campaign.php" class="btn btn-secondary ms-2">Back to Campaigns</a>
  </form>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
