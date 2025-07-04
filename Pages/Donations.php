<?php
session_start();
require_once('../includes/db_connect.php');

$campaignId = $_GET['campaign_id'] ?? null;
if (!$campaignId) {
    die("Campaign not specified.");
}

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'guest';

$donorName = '';
$donorEmail = '';

// Prefill info if donor is logged in
if ($role === 'donor') {
    $stmt = $conn->prepare("SELECT username AS full_name, email FROM users WHERE user_id = ?");

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($donorName, $donorEmail);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate to Campaign</title>
    <link rel="stylesheet" href="../CSS/donations.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="donation-page">

<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
    <h2 class="mb-4">Make a Donation</h2>
    
    <form action="../Processes/process_donation.php" method="POST">
    <input type="hidden" name="campaign_id" value="<?= htmlspecialchars($campaignId) ?>">

    <?php if ($role !== 'donor'): ?>
        <!-- Guest donor fields -->
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number (Format: 2547XXXXXXXX)</label>
            <input type="text" name="phone" class="form-control" required pattern="2547[0-9]{8}" title="Enter a valid phone number starting with 2547...">
        </div>
    <?php else: ?>
        <!-- Logged-in donor info display -->
        <input type="hidden" name="donor_id" value="<?= htmlspecialchars($userId) ?>">
<input type="hidden" name="full_name" value="<?= htmlspecialchars($donorName) ?>">
<input type="hidden" name="email" value="<?= htmlspecialchars($donorEmail) ?>">

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($donorName) ?>" disabled>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($donorEmail) ?>" disabled>
</div>
<div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="text" name="phone" class="form-control" required>
</div>

        </div>
    <?php endif; ?>

    <div class="mb-3">
        <label for="amount" class="form-label">Amount (KES)</label>
        <input type="number" name="amount" class="form-control" required min="10">
    </div>

    <div class="mb-3">
        <label for="payment_mode" class="form-label">Payment Mode</label>
        <select name="payment_mode" class="form-select" required>
            <option value="">-- Select Payment Mode --</option>
            <option value="M-Pesa">M-Pesa</option>
            <option value="Bank">Bank</option>
            <option value="Card">Card</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Donate Now</button>
</form>

</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
