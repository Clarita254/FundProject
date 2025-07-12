<?php
require_once('../includes/db_connect.php');
require_once('../includes/mpesa_utils.php');

header("Content-Type:application.json");
// As an Mpesa Daraja developer with 10 years experience advise me on my php callback file 
// Here is a sample 

// I want to Get the callback data using this code for my file to get the receipt number
// I am using ngrok to expose the file as "CallBackURL": "https://8b43-105-163-0-127.ngrok-free.app/EDUFUNDPROJECT/FundProject/mpesa/stk_callback.php",
// But my php file is not receiving the data and hence not writing data to the database
// what changes can i make to this file to make it work so that the data is received and written to the data base using localhost xampp and ngrok as decscribed above
// I have litte experience with catching php callback functions.
// show me step by step how to resolve this and explain the solution concisely in 5 lines at the end of your steps
$data = file_get_contents('php://input');


file_put_contents("stk_callback_raw.txt", $data . PHP_EOL, FILE_APPEND);

$response = json_decode($data, true);
$stkCallback = $response['Body']['stkCallback'] ?? null;

if (!$stkCallback) {
    http_response_code(400);
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback']);
    exit;
}

$ResultCode = $stkCallback['ResultCode'];
$CheckoutRequestID = $stkCallback['CheckoutRequestID'];

$Amount = 0;
$MpesaReceiptNumber = '';
$PhoneNumber = '';

$metadataItems = $stkCallback['CallbackMetadata']['Item'] ?? [];
foreach ($metadataItems as $item) {
    if ($item['Name'] === 'Amount') {
        $Amount = $item['Value'];
    } elseif ($item['Name'] === 'MpesaReceiptNumber') {
        $MpesaReceiptNumber = $item['Value'];
    } elseif ($item['Name'] === 'PhoneNumber') {
        $PhoneNumber = $item['Value'];
    }
}


if ($ResultCode == 0 && $MpesaReceiptNumber) {
    $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', mpesa_receipt = ?, phone_number = ? WHERE checkout_request_id = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $MpesaReceiptNumber, $PhoneNumber, $CheckoutRequestID);
        $stmt->execute();
        $stmt->close();
    } else {
        file_put_contents("stk_callback_errors.txt", "Prepare failed: " . $conn->error . PHP_EOL, FILE_APPEND);
    }
} else {
    // Transaction Failed or Cancelled
    $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE checkout_request_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $CheckoutRequestID);
        $stmt->execute();
        $stmt->close();
        echo(":failed");
    } else {
        file_put_contents("stk_callback_errors.txt", "Prepare failed (failed update): " . $conn->error . PHP_EOL, FILE_APPEND);
    }
}
// Return JSON response to Safaricom
// echo json_encode(["ResultCode" => 0, "ResultDesc" => "Callback received successfully"]);
header("location: payment_status.php");
?>
