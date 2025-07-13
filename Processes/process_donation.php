<?php
session_start();
require_once('../includes/db_connect.php');
require_once('../mpesa/Mpesa-utils.php'); // STK Push function

// Helper function to generate username
function generateUsernameFromFullName($full_name) {
    $base = strtolower(preg_replace('/\s+/', '', $full_name));
    return $base . rand(1000, 9999);
}

// Collect and validate POST data
$campaign_id  = $_POST['campaign_id'] ?? null;
$full_name    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$amount       = (float)($_POST['amount'] ?? 0);
$payment_mode = trim($_POST['payment_mode'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$status       = 'Pending';

if (!$campaign_id || $amount < 10 || !$payment_mode || !$email || !$full_name || !$phone) {
    die("Missing or invalid donation data.");
}

$isNewUser = false;

// Check if donor exists
$stmt = $conn->prepare("SELECT user_id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $donorId = $user['user_id'];
    $username = $user['username'];
} else {
    // Register new donor
    $username = generateUsernameFromFullName($full_name);
    $defaultPassword = password_hash('default123', PASSWORD_DEFAULT);
    $role = 'donor';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, ?, TRUE)");
    $stmt->bind_param("ssss", $username, $email, $defaultPassword, $role);
    $stmt->execute();
    $donorId = $stmt->insert_id;
    $stmt->close();

    $isNewUser = true;
}

// Start donor session
$_SESSION['user_id'] = $donorId;
$_SESSION['role'] = 'donor';
$_SESSION['username'] = $username;

if ($isNewUser) {
    $_SESSION['is_new_donor'] = true;
    $_SESSION['username_generated'] = $username;
}

// Insert donation
$stmt = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, payment_mode, status, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iidss", $donorId, $campaign_id, $amount, $payment_mode, $status);
$stmt->execute();
$donation_id = $stmt->insert_id;
$stmt->close();

// Store donation ID in session for later status check
$_SESSION['donation_id'] = $donation_id;

// If M-Pesa selected, initiate STK push
if ($payment_mode === 'M-Pesa') {
    $response = initiateStkPush($conn, $phone, $amount, $donation_id);
    
    if (!$response['success']) {
        // Optionally: update donation as failed
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE donation_Id = ?");
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $stmt->close();

        die("M-Pesa STK Push failed: " . $response['message']);
    }
}

// Store for thank you screen
$_SESSION['donor_name'] = $full_name;
$_SESSION['donated_amount'] = $amount;

// Redirect
header("Location: ../Pages/thankyou.php");
exit();
?> 