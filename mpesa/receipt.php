<?php
require_once('../includes/db_connect.php');
session_start();

// Only logged-in donors allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Step 1: Get the actual donor_Id from the donors table using session user_id
$sessionUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT donor_Id FROM donors WHERE user_id = ?");
$stmt->bind_param("i", $sessionUserId);
$stmt->execute();
$result = $stmt->get_result();
$donorData = $result->fetch_assoc();
$stmt->close();

if (!$donorData) {
    die("Donor profile not found.");
}

$donorId = $donorData['donor_Id'];

// Step 2: Now fetch the donation using donor_Id
$donationId = $_GET['donation_Id'] ?? null;

$stmt = $conn->prepare("SELECT d.*, c.campaign_name FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donation_Id = ? AND d.donor_Id = ?");
$stmt->bind_param("ii", $donationId, $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();

if (!$donation) {
    die("Donation not found.");
}

$status = $donation['status'];
$isProvisional = $status !== 'Completed';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donation Receipt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/receipt.css">
</head>
<body>

<div class="receipt-card">
  <div class="receipt-header">
    <h2><i class="fas fa-receipt me-2"></i>EduFund Donation Receipt</h2>
  </div>

  <div class="receipt-body mt-4">
    <?php if ($isProvisional): ?>
      <div class="alert alert-warning">
        <strong>Note:</strong> This is a provisional receipt. The payment is still being verified by M-Pesa.
      </div>
    <?php endif; ?>

    <p><strong>Receipt No:</strong> <?= htmlspecialchars($donation['mpesa_receipt']) ?></p>
    <p><strong>Campaign:</strong> <?= htmlspecialchars($donation['campaign_name']) ?></p>
    <p><strong>Amount Donated:</strong> <span class="badge bg-primary">KES <?= number_format($donation['amount'], 2) ?></span></p>
    <p><strong>Payment Mode:</strong> <?= htmlspecialchars($donation['payment_mode']) ?></p>
    <p><strong>Status:</strong> 
      <span class="badge <?= $status === 'Completed' ? 'bg-success' : ($status === 'Failed' ? 'bg-danger' : 'bg-warning text-dark') ?> badge-status">
        <?= htmlspecialchars($status) ?>
      </span>
    </p>
    <p><strong>Phone Number:</strong> <?= htmlspecialchars($donation['phone_number']) ?></p>
    <p><strong>Donation Date:</strong> <?= date("F j, Y, g:i a", strtotime($donation['donation_date'])) ?></p>

    <p class="thank-you">Thank you for supporting education through EduFund.</p>

    <div class="btn-group">
      <a href="../mpesa/receipt_pdf.php?donation_Id=<?= $donation['donation_Id'] ?>" class="btn btn-success me-2" target="_blank">
        <i class="fas fa-download me-1"></i>Download PDF
      </a>
      <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print me-1"></i>Print Receipt
      </button>
    </div>
  </div>
</div>

</body>
</html>

