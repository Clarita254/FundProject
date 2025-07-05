<?php
require_once('../includes/db_connect.php');

// Read JSON input
$data = file_get_contents('php://input');
$logFile = "stk_callback_log.txt";
file_put_contents($logFile, $data . PHP_EOL, FILE_APPEND); // log for debugging

$response = json_decode($data, true);

// Extract details
$stkCallback = $response['Body']['stkCallback'] ?? null;

if ($stkCallback) {
    $ResultCode = $stkCallback['ResultCode'];
    $CheckoutRequestID = $stkCallback['CheckoutRequestID'];
    $MpesaReceiptNumber = $stkCallback['CallbackMetadata']['Item'][1]['Value'] ?? null;
    $PhoneNumber = $stkCallback['CallbackMetadata']['Item'][4]['Value'] ?? null;

    if ($ResultCode == 0 && $MpesaReceiptNumber) {
        // Update donation
        $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ? WHERE checkout_request_id = ?");
        $stmt->bind_param("sss", $MpesaReceiptNumber, $PhoneNumber, $CheckoutRequestID);
        $stmt->execute();
        $stmt->close();
    } else {
        // Mark as failed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE checkout_request_id = ?");
        $stmt->bind_param("s", $CheckoutRequestID);
        $stmt->execute();
        $stmt->close();
    }
}
?>
