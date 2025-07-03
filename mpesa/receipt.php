<?php
require_once('../includes/db_connect.php');
session_start();

// Only logged-in donors allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$donorId = $_SESSION['user_id'];
$donationId = $_GET['donation_id'] ?? null;

// Fetch donation with campaign
$stmt = $conn->prepare("SELECT d.*, c.campaign_name FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donation_id = ? AND d.donor_id = ?");
$stmt->bind_param("ii", $donationId, $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();

if (!$donation || $donation['status'] !== 'Completed') {
    die("Receipt not available. Donation either failed or is still pending.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donation Receipt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <a href="receipt_pdf.php?donation_id=<?= $donation['donation_id'] ?>" target="_blank" class="btn btn-outline-success mt-2">
    Download PDF</a>

</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h2 class="mb-4 text-center text-success">Donation Receipt</h2>

    <p><strong>Receipt No:</strong> <?= htmlspecialchars($donation['mpesa_receipt']) ?></p>
    <p><strong>Campaign:</strong> <?= htmlspecialchars($donation['campaign_name']) ?></p>
    <p><strong>Amount Donated:</strong> KES <?= number_format($donation['amount'], 2) ?></p>
    <p><strong>Payment Mode:</strong> <?= htmlspecialchars($donation['payment_mode']) ?></p>
    <p><strong>Phone Number:</strong> <?= htmlspecialchars($donation['phone_number']) ?></p>
    <p><strong>Donation Date:</strong> <?= date("F j, Y, g:i a", strtotime($donation['donation_date'])) ?></p>

    <hr>
    <p class="text-muted">Thank you for supporting education through EduFund.</p>

    <!-- Option to print or download -->
    <div class="text-center mt-4">
      <button onclick="window.print()" class="btn btn-outline-primary">Print Receipt</button>
    </div>
  </div>
</div>
</body>
</html>
