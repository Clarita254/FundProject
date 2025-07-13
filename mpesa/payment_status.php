<?php
session_start();
require_once('../includes/db_connect.php');
require_once('../mpesa/stk_status_query.php'); // Contains queryAndUpdateDonationStatus()

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$userId = $_SESSION['user_id'];

// ✅ Step 1: Get actual donor_Id from donors table
$stmt = $conn->prepare("SELECT donor_Id FROM donors WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$donorRow = $result->fetch_assoc();
$stmt->close();

if (!$donorRow) {
    die("Donor profile not found.");
}
$donorId = $donorRow['donor_Id'];

// ✅ Step 2: Get the most recent donation using correct donor_Id
$stmt = $conn->prepare("SELECT d.*, c.campaign_name 
                        FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donor_Id = ?
                        ORDER BY d.donation_date DESC
                        LIMIT 1");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
$latest = $result->fetch_assoc();
$stmt->close();

// ✅ Step 3: Run STK status query if needed
if ($latest && $latest['payment_mode'] === 'M-Pesa' && $latest['status'] === 'Pending') {
    queryAndUpdateDonationStatus($conn, $latest['donation_Id']);
}

// ✅ Step 4: Re-fetch updated donation
$stmt = $conn->prepare("SELECT d.*, c.campaign_name 
                        FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donor_Id = ?
                        ORDER BY d.donation_date DESC
                        LIMIT 1");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/paymentstatus.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
    <?php if ($donation): ?>
        <div class="status-card mx-auto text-center p-4 shadow-sm bg-white rounded" style="max-width: 600px;">
            <?php
            $status = $donation['status'];
            $statusColor = $status === 'Completed' ? 'text-success' : ($status === 'Failed' ? 'text-danger' : 'text-warning');
            $icon = $status === 'Completed' ? '✅' : ($status === 'Failed' ? '❌' : '⏳');
            ?>

            <div class="status-icon <?= $statusColor ?>" style="font-size: 3rem;"><?= $icon ?></div>
            <h3 class="status-header mt-3">Donation Status</h3>
            <hr>

            <p><strong>Campaign:</strong> <?= htmlspecialchars($donation['campaign_name']) ?></p>
            <p><strong>Amount Donated:</strong> <span class="text-primary">KES <?= number_format($donation['amount'], 2) ?></span></p>
            <p><strong>Status:</strong> <span class="fw-bold <?= $statusColor ?>"><?= htmlspecialchars($status) ?></span></p>

            <?php if (!empty($donation['donation_Id'])): ?>
                <a href="../mpesa/receipt.php?donation_Id=<?= $donation['donation_Id'] ?>" class="btn btn-receipt mt-3">Download Receipt</a>
            <?php endif; ?>

            <?php if ($status !== 'Completed'): ?>
                <div class="alert alert-warning mt-3">
                    <strong>Note:</strong> This receipt is provisional. The payment is still being verified by M-Pesa.
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center mt-5">
            You haven’t made any donations yet.
        </div>
    <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
