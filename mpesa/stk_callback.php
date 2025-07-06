<?php
require_once('../includes/db_connect.php');

// Capture raw input and log for debugging
$data = file_get_contents('php://input');
file_put_contents("stk_callback_log.txt", print_r(json_decode($data, true), true) . PHP_EOL, FILE_APPEND);

$response = json_decode($data, true);
$stkCallback = $response['Body']['stkCallback'] ?? null;

if ($stkCallback) {
    $ResultCode = $stkCallback['ResultCode'];
    $CheckoutRequestID = $stkCallback['CheckoutRequestID'];

    $MpesaReceiptNumber = null;
    $PhoneNumber = null;

    // Safely extract fields from CallbackMetadata
    $metadataItems = $stkCallback['CallbackMetadata']['Item'] ?? [];

    foreach ($metadataItems as $item) {
        if ($item['Name'] === 'MpesaReceiptNumber') {
            $MpesaReceiptNumber = $item['Value'];
        }
        if ($item['Name'] === 'PhoneNumber') {
            $PhoneNumber = $item['Value'];
        }
    }

    if ($ResultCode == 0 && $MpesaReceiptNumber) {
        // Update donation as completed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ? WHERE checkout_request_id = ?");
        if ($stmt) {
            $stmt->bind_param("sss", $MpesaReceiptNumber, $PhoneNumber, $CheckoutRequestID);
            $stmt->execute();
            $stmt->close();
        } else {
            file_put_contents("stk_callback_log.txt", "DB Update Failed: " . $conn->error . PHP_EOL, FILE_APPEND);
        }
    } else {
        // Update donation as failed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE checkout_request_id = ?");
        if ($stmt) {
            $stmt->bind_param("s", $CheckoutRequestID);
            $stmt->execute();
            $stmt->close();
        } else {
            file_put_contents("stk_callback_log.txt", "DB Fail Update Failed: " . $conn->error . PHP_EOL, FILE_APPEND);
        }
    }
}
?>
