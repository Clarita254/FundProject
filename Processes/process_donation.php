<?php
require_once('../includes/db_connect.php');
require_once('../mpesa/mpesa_utils.php'); // Contains STK push functions
session_start();

function generateUsernameFromFullName($full_name) {
    $base = strtolower(str_replace(' ', '', $full_name));
    return $base . rand(1000, 9999); // e.g., marynjoki3478
}

// Collect and validate POST data
$campaign_id = $_POST['campaign_id'] ?? null;
$full_name   = trim($_POST['full_name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$amount      = (float)($_POST['amount'] ?? 0);
$payment_mode = $_POST['payment_mode'] ?? '';
$phone       = trim($_POST['phone'] ?? '');
$status = 'Pending';

if (!$campaign_id || $amount <= 0 || empty($payment_mode) || empty($email) || empty($full_name) || empty($phone)) {
    die("Invalid donation data.");
}

$isNewUser = false;

// Check if user already exists
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
    $username = generateUsernameFromFullName($full_name);
    $hashedPassword = password_hash('default123', PASSWORD_DEFAULT);
    $role = 'donor';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, ?, TRUE)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    $stmt->execute();
    $donorId = $stmt->insert_id;
    $stmt->close();

    $isNewUser = true;
}

// Save donor session info
$_SESSION['user_id'] = $donorId;
$_SESSION['role'] = 'donor';

// Insert pending donation
$stmt = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, payment_mode, status, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iidss", $donorId, $campaign_id, $amount, $payment_mode, $status);
$stmt->execute();
$donation_id = $stmt->insert_id;
$stmt->close();

// Trigger M-Pesa STK Push (only if payment mode is M-Pesa) //Call STK push and save pending donation
if ($payment_mode === 'M-Pesa') {
    $response = initiateStkPush($phone, $amount, $donation_id);

    if (!$response['success']) {
        die("M-Pesa payment initiation failed: " . $response['message']);
    }
}

// Set session for thank you page
$_SESSION['donor_name'] = $full_name;
$_SESSION['donated_amount'] = $amount;

if ($isNewUser) {
    $_SESSION['is_new_donor'] = true;
    $_SESSION['username_generated'] = $username;
}

header("Location: thankyou.php");
exit();
?>
