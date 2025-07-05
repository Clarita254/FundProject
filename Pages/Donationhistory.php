<?php
session_start();
require_once('../includes/db_connect.php');

// Ensure only donors access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/login.php");
    exit();
}

$donorId = $_SESSION['user_id'];
$donationHistory = [];

// Fetch donation history
$stmt = $conn->prepare("
    SELECT 
        d.donation_date,
        c.campaign_name,
        d.amount,
        d.payment_mode,
        d.status
    FROM donations d
    JOIN campaigns c ON d.campaign_id = c.campaign_id
    WHERE d.donor_id = ?
    ORDER BY d.donation_date DESC
");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $donationHistory[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4 text-primary">Your Donation History</h2>

    <?php if (count($donationHistory) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Campaign</th>
                        <th>Amount Donated</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donationHistory as $donation): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($donation['donation_date']))) ?></td>
                            <td><?= htmlspecialchars($donation['campaign_name']) ?></td>
                            <td>KES <?= number_format($donation['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($donation['payment_mode']) ?></td>
                            <td>
                                <?php
                                $status = $donation['status'];
                                $badgeClass = match ($status) {
                                    'Completed' => 'success',
                                    'Pending' => 'warning',
                                    'Failed' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            You haven't made any donations yet.
        </div>
    <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
