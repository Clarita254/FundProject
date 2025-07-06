<?php
require_once('../includes/db_connect.php');
require_once('../vendor/autoload.php');
session_start();

use Dompdf\Dompdf;
use Dompdf\Options;

// Only logged-in donors allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    die("Access denied.");
}

$donorId = $_SESSION['user_id'];
$donationId = $_GET['donation_Id'] ?? null;

// Fetch donation and campaign data
$stmt = $conn->prepare("SELECT d.*, c.campaign_name FROM donations d
                        JOIN campaigns c ON d.campaign_id = c.campaign_id
                        WHERE d.donation_Id = ? AND d.donor_id = ?");
$stmt->bind_param("ii", $donationId, $donorId);
$stmt->execute();
$result = $stmt->get_result();
$donation = $result->fetch_assoc();
$stmt->close();

if (!$donation || $donation['status'] !== 'Completed') {
    die("Receipt not available. Donation either failed or is still pending.");
}

// Load external CSS
$css = file_get_contents('../CSS/receipt_pdf.css');

// Load EduFund logo and convert to base64
$logoPath = '../assets/Edufund.png';
$logoBase64 = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,' . $logoBase64;

// Build HTML content
$html = '
<html>
<head>
    <meta charset="UTF-8">
    <style>' . $css . '</style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="receipt-header">
            <img src="' . $logoSrc . '" alt="EduFund Logo" style="max-width: 120px; margin-bottom: 10px;">
            <h2>EduFund Donation Receipt</h2>
        </div>
        <div class="receipt-body">
            <p><strong>Receipt No:</strong> ' . htmlspecialchars($donation['mpesa_receipt']) . '</p>
            <p><strong>Campaign:</strong> ' . htmlspecialchars($donation['campaign_name']) . '</p>
            <p><strong>Amount Donated:</strong> KES ' . number_format($donation['amount'], 2) . '</p>
            <p><strong>Payment Mode:</strong> ' . htmlspecialchars($donation['payment_mode']) . '</p>
            <p><strong>Phone Number:</strong> ' . htmlspecialchars($donation['phone_number']) . '</p>
            <p><strong>Donation Date:</strong> ' . date("F j, Y, g:i a", strtotime($donation['donation_date'])) . '</p>
            <div class="thank-you">Thank you for supporting education through EduFund</div>
        </div>
    </div>
</body>
</html>
';

// Setup Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->setIsRemoteEnabled(true);
$options->setChroot(realpath(__DIR__)); // Allow local paths

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Donation_Receipt_{$donationId}.pdf", ["Attachment" => false]);
exit;
?>
