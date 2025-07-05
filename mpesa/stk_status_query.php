<?php
require_once('../includes/db_connect.php');
require_once('Mpesa-utils.php');




// Query and update STK Push status
function queryAndUpdateDonationStatus($conn, $donation_id) {

    file_put_contents('stk_query_log.txt', "[" . date('Y-m-d H:i:s') . "] Entered status query for donation ID: $donation_id\n", FILE_APPEND);
    // Fetch CheckoutRequestID from donations table
    $stmt = $conn->prepare("SELECT checkout_request_id FROM donations WHERE donation_id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $stmt->bind_result($checkoutRequestID);
    $stmt->fetch();
    $stmt->close();

    if (empty($checkoutRequestID)) {
        file_put_contents('stk_query_log.txt', "No checkout_request_id for donation_id: $donation_id\n", FILE_APPEND);
        return;
    }

    $consumerKey = 'Ehu5LsxxuGGCAbWJNF4IRmWb59pUuiKCmzKHa4yRo93VAEgL';
    $consumerSecret = '1qq7GVSAXNAKFOmbXD4i6acBvNdAomhcJGaS5iSHLa4Os19U1juegA01RPsGSoZ9';
    $accessToken = generateAccessToken($consumerKey, $consumerSecret);

    if (!$accessToken) {
        file_put_contents('stk_query_log.txt', "Access token generation failed.\n", FILE_APPEND);
        return;
    }

    $shortcode = '174379';
    $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    $payload = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'CheckoutRequestID' => $checkoutRequestID
    ];

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $result = json_decode($response, true);

    // Logging full result
    file_put_contents('stk_query_log.txt', "[" . date('Y-m-d H:i:s') . "] Donation ID: $donation_id\nHTTP: $http_code\nResponse: $response\n\n", FILE_APPEND);

    if (!isset($result['ResultCode'])) {
        return; // No result code = invalid response
    }

    $resultCode = $result['ResultCode'];
    $resultDesc = $result['ResultDesc'] ?? 'No description';

    if ($resultCode == '0') {
        // Success → mark as Completed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Completed' WHERE donation_id = ?");
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($resultCode == '1032') {
        // Transaction cancelled by user → optional to mark as Failed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE donation_id = ?");
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($resultCode == '1') {
        // Still Pending → do nothing or keep retrying
    } else {
        // Log failure reason and update donation if necessary
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE donation_id = ?");
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>
