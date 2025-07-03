<?php
// mpesa_utils.php - handles M-Pesa token generation and STK Push

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

/**
 * Triggers STK Push to a phone number
 * 
 * @param string $phone  MSISDN (format: 2547XXXXXXXX)
 * @param float $amount
 * @param int $donation_id Used as AccountReference
 * @return array ['success' => bool, 'message' => string]
 */
function initiateStkPush($phone, $amount, $donation_id) {
    $consumerKey = 'OsZjpPFLlXVHmY4b3qeJLVpQmmxOOBC3YMs9BrdGtorLgJi4';  // Replace with your actual key
    $consumerSecret = 'OkBZIePY2bcUW47mNSteRjExqXnIMP8xQcWG4tEenHAC0HVSYiO5GBwpIRegGAYS';  // Replace with your actual secret
    $shortcode = '174379'; // Safaricom test paybill
    $passkey = 'YOUR_PASSKEY'; // Replace with your test/production passkey
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);

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
        'PartyA' => $phone,
        'PartyB' => $shortcode,
        'PhoneNumber' => $phone,
        'CallBackURL' => 'https://edufund.ke/mpesa/mpesa_callback.php',
        'AccountReference' => $donation_id, // to help identify in callback
        'TransactionDesc' => "Donation to campaign #$donation_id"
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
        return ['success' => true, 'message' => 'STK Push initiated successfully'];
    } else {
        return ['success' => false, 'message' => $result['errorMessage'] ?? 'STK Push failed'];
    }
}
?>
