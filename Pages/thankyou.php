<?php
session_start();
require_once('../includes/db_connect.php');
require_once('../mpesa/stk_status_query.php'); // contains queryAndUpdateDonationStatus()

// Check donation status only if donation_id is available in session
if (isset($_SESSION['donation_id'])) {
    $donation_id = $_SESSION['donation_id'];

    // Query donation to confirm if it's M-Pesa and still pending
    $stmt = $conn->prepare("SELECT payment_mode, status FROM donations WHERE donation_id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();
    $stmt->close();

    if ($donation && $donation['payment_mode'] === 'M-Pesa' && $donation['status'] === 'Pending') {
        queryAndUpdateDonationStatus($conn, $donation_id);
    }
}

// Donor info from session
$donorName = $_SESSION['donor_name'] ?? 'Donor';
$donatedAmount = $_SESSION['donated_amount'] ?? 0;
$isNew = $_SESSION['is_new_donor'] ?? false;
$username = $_SESSION['username_generated'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank You for Donating!</title>
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5 text-center">
    <h2 class="text-success">🎉 Thank You, <?= htmlspecialchars($donorName) ?>!</h2>
    <p class="lead">Your donation of <strong>KES <?= number_format($donatedAmount, 2) ?></strong> has been received.</p>

    <?php if ($isNew): ?>
        <div class="alert alert-info mt-4">
            <p><strong>Note:</strong> An account has been created for you!</p>
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Default Password:</strong> <code>default123</code> (Please change after logging in)</p>
        </div>
    <?php endif; ?>

    <a href="../Dashboards/donorDashboard.php" class="btn btn-primary mt-3">Go to Donor Dashboard</a>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<!-- Auto-redirect to status page -->
<script>
    setTimeout(() => {
        window.location.href = '../mpesa/payment_status.php';
    }, 15000); // 15 seconds
</script>

</body>
</html>

<?php
// Clear flash session data (leave donation_id for status page to access)
unset($_SESSION['donor_name'], $_SESSION['donated_amount'], $_SESSION['is_new_donor'], $_SESSION['username_generated']);
?>
