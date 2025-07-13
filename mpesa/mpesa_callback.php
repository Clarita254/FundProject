<?php
file_put_contents("callback_log.txt", "Callback hit at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);

require_once('../includes/db_connect.php');

$callbackJSON = file_get_contents('php://input');
file_put_contents('callback_raw_log.txt', $callbackJSON . PHP_EOL, FILE_APPEND);

$data = json_decode($callbackJSON, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON']);
    exit;
}

$stkCallback = $data['Body']['stkCallback'] ?? null;
if (!$stkCallback) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing stkCallback']);
    exit;
}

$checkoutRequestID = $stkCallback['CheckoutRequestID'];
$resultCode = $stkCallback['ResultCode'];
$resultDesc = $stkCallback['ResultDesc'];

$amount = 0;
$mpesaReceiptNumber = '';
$phoneNumber = '';

if ($resultCode === 0) {
    // Extract values from callback metadata
    foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
        if ($item['Name'] === 'Amount') {
            $amount = $item['Value'];
        } elseif ($item['Name'] === 'MpesaReceiptNumber') {
            $mpesaReceiptNumber = $item['Value'];
        } elseif ($item['Name'] === 'PhoneNumber') {
            $phoneNumber = $item['Value'];
        }
    }

    // 1. ✅ Update the donations table
    $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ?, donation_date = NOW() WHERE checkout_request_id = ?");
    $stmt->bind_param("sss", $mpesaReceiptNumber, $phoneNumber, $checkoutRequestID);
    $stmt->execute();
    $stmt->close();

    // 2. ✅ Get campaign_id from donation
    $stmt = $conn->prepare("SELECT campaign_id FROM donations WHERE checkout_request_id = ?");
    $stmt->bind_param("s", $checkoutRequestID);
    $stmt->execute();
    $stmt->bind_result($campaignId);
    $stmt->fetch();
    $stmt->close();


} else {
    // ❌ Update status to failed
    $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE checkout_request_id = ?");
    $stmt->bind_param("s", $checkoutRequestID);
    $stmt->execute();
    $stmt->close();
}

// ✅ Final debug log
$log = "=== Callback @ " . date('Y-m-d H:i:s') . " ===\n";
$log .= "Result: $resultCode ($resultDesc)\n";
$log .= "CheckoutRequestID: $checkoutRequestID\n";
$log .= "Receipt: $mpesaReceiptNumber\n";
$log .= "Phone: $phoneNumber\n";
$log .= "Amount: $amount\n\n";
file_put_contents('callback_debug_log.txt', $log, FILE_APPEND);

// ✅ Respond to Safaricom
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Callback received successfully']);
?>
