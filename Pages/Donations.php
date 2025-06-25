<?php
require_once("../includes/db_connect.php");
session_start();

// Ensure only donors can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: signIn.php");
    exit();
}

$donorId = $_SESSION['user_id'];
$campaignId = isset($_GET['campaign_id']) ? (int)$_GET['campaign_id'] : 0;

// Fetch campaign details
$campaign = null;
if ($campaignId > 0) {
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE campaign_id = ?");
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $campaign = $result->fetch_assoc();
    $stmt->close();
}

if (!$campaign) {
    echo "<div class='alert alert-danger'>Campaign not found.</div>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $paymentMode = $_POST['payment_mode'];
    $status = "Pending"; // default status

    $stmt = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, donation_date, payment_mode, status)
                            VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iidss", $donorId, $campaignId, $amount, $paymentMode, $status);

    if ($stmt->execute()) {
        header("Location: Donationhistory.php?success=1");
        exit();
    } else {
        $errorMsg = "Error processing donation. Please try again.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate to Campaign</title>
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
    <h2 class="mb-4">Donate to: <span class="text-primary"><?= htmlspecialchars($campaign['title']); ?></span></h2>

    <?php if (isset($errorMsg)): ?>
        <div class="alert alert-danger"><?= $errorMsg; ?></div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
        <p><strong>Description:</strong> <?= htmlspecialchars($campaign['description']); ?></p>
        <p><strong>Target Amount:</strong> KES <?= number_format($campaign['target_amount'], 2); ?></p>
        <p><strong>Raised so far:</strong> KES <?= number_format($campaign['amount_raised'], 2); ?></p>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="amount" class="form-label">Donation Amount (KES)</label>
                <input type="number" name="amount" class="form-control" required min="50" step="0.01">
            </div>

            <div class="mb-3">
                <label for="payment_mode" class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-select" required>
                    <option value="">Select Payment Mode</option>
                    <option value="M-PESA">M-PESA</option>
                    <option value="Card">Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Donate Now</button>
            <a href="campaign.php" class="btn btn-secondary ms-2">Back to Campaigns</a>
        </form>
    </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
