<?php
function generateAccessToken($consumerKey, $consumerSecret) {
    $credentials = base64_encode("$consumerKey:$consumerSecret");
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => ["Authorization: Basic $credentials"],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response);
    return $result->access_token ?? null;
}

function initiateStkPush($conn, $phone, $amount, $donation_id) {
    $consumerKey = 'Ehu5LsxxuGGCAbWJNF4IRmWb59pUuiKCmzKHa4yRo93VAEgL';

    $consumerSecret = '1qq7GVSAXNAKFOmbXD4i6acBvNdAomhcJGaS5iSHLa4Os19U1juegA01RPsGSoZ9';


    $shortcode = '174379';
    $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

    $accessToken = generateAccessToken($consumerKey, $consumerSecret);
    if (!$accessToken) {
        return ['success' => false, 'message' => 'Failed to generate access token'];
    }

    $payload = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL'=> "https://8b43-105-163-0-127.ngrok-free.app/EDUFUNDPROJECT/FundProject/mpesa/stk_callback.php",
        'AccountReference' => "DON-$donation_id",
        'TransactionDesc' => "Donation to campaign #$donation_id"
    ];

    $curl = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);
    if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
        $checkoutRequestID = $result['CheckoutRequestID'];

        // Save to DB
        $stmt = $conn->prepare("UPDATE donations SET checkout_request_id = ? WHERE donation_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $checkoutRequestID, $donation_id);
            $stmt->execute();
            $stmt->close();
        } else {
            file_put_contents('callback_errors.txt', "STK update failed: " . $conn->error . PHP_EOL, FILE_APPEND);
        }

        return ['success' => true, 'checkoutRequestID' => $checkoutRequestID];
    } else {
        return ['success' => false, 'message' => $result['errorMessage'] ?? 'Unknown error'];
    }
}
?>