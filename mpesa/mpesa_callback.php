<?php
require_once('../includes/db_connect.php');

// Read the raw input
$callbackJSON = file_get_contents('php://input');
$data = json_decode($callbackJSON, true);

// Log the callback for debugging
file_put_contents('mpesa_callback_log.txt', $callbackJSON . PHP_EOL, FILE_APPEND);

if (!$data) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON']);
    exit;
}

// Parse the response
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
    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'];

    foreach ($callbackMetadata as $item) {
        if ($item['Name'] === 'Amount') {
            $amount = $item['Value'];
        } elseif ($item['Name'] === 'MpesaReceiptNumber') {
            $mpesaReceiptNumber = $item['Value'];
        } elseif ($item['Name'] === 'PhoneNumber') {
            $phoneNumber = $item['Value'];
        }
    }

    // Update the donation record
    $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ? 
                            WHERE checkout_request_id = ?");
    $stmt->bind_param("sss", $mpesaReceiptNumber, $phoneNumber, $checkoutRequestID);
    $stmt->execute();
    $stmt->close();
} else {
    // Update the donation as failed
    $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' 
                            WHERE checkout_request_id = ?");
    $stmt->bind_param("s", $checkoutRequestID);
    $stmt->execute();
    $stmt->close();
}

// Respond with success
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Received successfully']);
?>
