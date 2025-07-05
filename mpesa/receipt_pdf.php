<?php
require_once('../includes/db_connect.php');
require_once('../vendor/autoload.php'); // Make sure Dompdf is installed
session_start();

use Dompdf\Dompdf;
use Dompdf\Options;

// Only logged-in donors allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    die("Access denied.");
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

// Start building HTML content for PDF
$html = '
    <h2 style="text-align: center; color: green;">EduFund Donation Receipt</h2>
    <hr>
    <p><strong>Receipt No:</strong> ' . htmlspecialchars($donation['mpesa_receipt']) . '</p>
    <p><strong>Campaign:</strong> ' . htmlspecialchars($donation['campaign_name']) . '</p>
    <p><strong>Amount Donated:</strong> KES ' . number_format($donation['amount'], 2) . '</p>
    <p><strong>Payment Mode:</strong> ' . htmlspecialchars($donation['payment_mode']) . '</p>
    <p><strong>Phone Number:</strong> ' . htmlspecialchars($donation['phone_number']) . '</p>
    <p><strong>Donation Date:</strong> ' . date("F j, Y, g:i a", strtotime($donation['donation_date'])) . '</p>
    <hr>
    <p style="color: gray;">Thank you for supporting education through EduFund.</p>
';

// PDF Options
$options = new Options();
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output the PDF to browser
$dompdf->stream("Donation_Receipt_{$donationId}.pdf", ["Attachment" => false]);
exit;
