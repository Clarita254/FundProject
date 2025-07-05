<?php  // Allow donors to view all completed donations and download a pdf receipt for each
require_once('../includes/db_connect.php');
session_start();

// Ensure donor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$donorId = $_SESSION['user_id'];

// Fetch all completed donations
$stmt = $conn->prepare("SELECT d.donation_id, d.amount, d.donation_date, d.mpesa_receipt, c.campaign_name
                        FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donor_id = ? AND d.status = 'Completed'
                        ORDER BY d.donation_date DESC");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Receipts</title>
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/myreceipts.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
    <h2 class="mb-4 text-primary text-center">My Donation Receipts</h2>

    <?php if (count($donations) === 0): ?>
        <div class="alert alert-warning">No completed donations found.</div>
    <?php else: ?>
        <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Campaign</th>
                <th>Amount (KES)</th>
                <th>Receipt No.</th>
                <th>Date</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donations as $index => $donation): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($donation['campaign_name']) ?></td>
                    <td><?= number_format($donation['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($donation['mpesa_receipt']) ?></td>
                    <td><?= date('M j, Y, g:i a', strtotime($donation['donation_date'])) ?></td>
                    <td>
                        <a href="receipt_pdf.php?donation_id=<?= $donation['donation_id'] ?>" class="btn btn-sm btn-outline-success">
    <i class="bi bi-file-earmark-pdf"></i> PDF
</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

    <?php endif; ?>
</div>

<?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
