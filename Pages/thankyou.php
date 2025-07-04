<?php
session_start();
header("Refresh: 15; URL=../mpesa/payment_status.php");


$donorName = $_SESSION['donor_name'] ?? 'Donor';
$donatedAmount = $_SESSION['donated_amount'] ?? '0.00';
$isNew = $_SESSION['is_new_donor'] ?? false;
$username = $_SESSION['username_generated'] ?? '';
?>

<?php
session_start();

$donor_name = $_SESSION['donor_name'] ?? 'Donor';
$amount = $_SESSION['donated_amount'] ?? 0;
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
    <h2 class="text-success">🎉 Thank You, <?= htmlspecialchars($donor_name) ?>!</h2>
    <p class="lead">Your donation of <strong>KES <?= number_format($amount, 2) ?></strong> has been received.</p>

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
</body>
</html>


<?php
// Clear the session messages
unset($_SESSION['donor_name'], $_SESSION['donated_amount'], $_SESSION['is_new_donor'], $_SESSION['username_generated']);
?>
