<?php
// mpesa_utils.php

function generateAccessToken($consumerKey, $consumerSecret) {
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
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

function initiateStkPush($phone, $amount, $donation_id) {
    //  Use your own consumer key and secret from your app
    $consumerKey = 'Ehu5LsxxuGGCAbWJNF4IRmWb59pUuiKCmzKHa4yRo93VAEgL';
    $consumerSecret = '1qq7GVSAXNAKFOmbXD4i6acBvNdAomhcJGaS5iSHLa4Os19U1juegA01RPsGSoZ9';

    // Safaricom official test Paybill shortcode
    $shortcode = '174379';

    //  Safaricom official test passkey (sandbox)
    $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode.$passkey.$timestamp);

    // Get access token
    $accessToken = generateAccessToken($consumerKey, $consumerSecret);
    if (!$accessToken) {
        return ['success' => false, 'message' => 'Failed to generate access token'];
    }

    $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $payload = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone, // Phone number to prompt STK
        'PartyB' => $shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://example.com/mpesa/mpesa_callback.php', // Replace with your real callback URL
        'AccountReference' => "DON-$donation_id",
        'TransactionDesc' => "Donation to campaign #$donation_id"
    ];

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json',
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode(json_encode(json_decode($response)), true);

    if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
        return ['success' => true, 'message' => 'STK Push initiated successfully'];
    } else {
        // dump($phone);
        return ['success' => false, 'message' => $result['errorMessage'] ?? 'STK Push failed'];
    }
}
