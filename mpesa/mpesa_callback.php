<?php

file_put_contents("callback_log.txt", "Callback hit at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);//To check if M-pesa is hit by safaricom

require_once('../includes/db_connect.php');

// Read the raw input
$callbackJSON = file_get_contents('php://input');
file_put_contents('callback_raw_log.txt', $callbackJSON . PHP_EOL, FILE_APPEND);

$data = json_decode($callbackJSON, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON']);
    exit;
}

// Extract STK Callback
$stkCallback = $data['Body']['stkCallback'] ?? null;
if (!$stkCallback) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing stkCallback']);
    exit;
}

$merchantRequestID = $stkCallback['MerchantRequestID'];
$checkoutRequestID = $stkCallback['CheckoutRequestID'];
$resultCode = $stkCallback['ResultCode'];
$resultDesc = $stkCallback['ResultDesc'];

$amount = 0;
$mpesaReceiptNumber = '';
$phoneNumber = '';

if ($resultCode === 0) {
    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

    foreach ($callbackMetadata as $item) {
        if ($item['Name'] === 'Amount') {
            $amount = $item['Value'];
        } elseif ($item['Name'] === 'MpesaReceiptNumber') {
            $mpesaReceiptNumber = $item['Value'];
        } elseif ($item['Name'] === 'PhoneNumber') {
            $phoneNumber = $item['Value'];
        }
    }

    // Update donation as Completed
    $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ? WHERE checkout_request_id = ?");
    if (!$stmt) {
        file_put_contents('callback_errors.txt', "Prepare failed: " . $conn->error . PHP_EOL, FILE_APPEND);
    } else {
        $stmt->bind_param("sss", $mpesaReceiptNumber, $phoneNumber, $checkoutRequestID);
        if (!$stmt->execute()) {
            file_put_contents('callback_errors.txt', "Execute failed: " . $stmt->error . PHP_EOL, FILE_APPEND);
        }
        $stmt->close();
    }

} else {
    // Update donation as Failed
    $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE checkout_request_id = ?");
    if (!$stmt) {
        file_put_contents('callback_errors.txt', "Prepare failed (failure update): " . $conn->error . PHP_EOL, FILE_APPEND);
    } else {
        $stmt->bind_param("s", $checkoutRequestID);
        if (!$stmt->execute()) {
            file_put_contents('callback_errors.txt', "Execute failed (failure update): " . $stmt->error . PHP_EOL, FILE_APPEND);
        }
        $stmt->close();
    }
}

// Log summary
$log = "=== Callback @ " . date('Y-m-d H:i:s') . " ===\n";
$log .= "Result: $resultCode ($resultDesc)\n";
$log .= "CheckoutRequestID: $checkoutRequestID\n";
$log .= "Receipt: $mpesaReceiptNumber\n";
$log .= "Phone: $phoneNumber\n\n";

file_put_contents('callback_debug_log.txt', $log, FILE_APPEND);

// Respond
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Callback received successfully']);
?>
