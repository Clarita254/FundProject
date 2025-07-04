<?php
require_once('../includes/db_connect.php');
require_once('../mpesa/Mpesa-utils.php'); // STK Push function
session_start();

// Generate username helper
function generateUsernameFromFullName($full_name) {
    $base = strtolower(preg_replace('/\s+/', '', $full_name));
    return $base . rand(1000, 9999);
}

// Collect POST data
$campaign_id  = $_POST['campaign_id'] ?? null;
$full_name    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$amount       = (float)($_POST['amount'] ?? 0);
$payment_mode = $_POST['payment_mode'] ?? '';
$phone        = trim($_POST['phone'] ?? '');
$status       = 'Pending';

// Validate required fields
if (!$campaign_id || $amount <= 0 || !$payment_mode || !$email || !$full_name || !$phone) {
    die("Missing or invalid donation data.");
}

$isNewUser = false;

// Check if donor already exists
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
    // Create new donor account
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

// Insert donation record
$stmt = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, payment_mode, status, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iidss", $donorId, $campaign_id, $amount, $payment_mode, $status);
$stmt->execute();
$donation_id = $stmt->insert_id;
$stmt->close();

// Trigger M-Pesa STK Push if applicable
if ($payment_mode === 'M-Pesa') {
    // $response = initiateStkPush('254708374149', $amount, $donation_id); //example
    $response = initiateStkPush('254708374149', $amount, $donation_id);

    if (!$response['success']) {
        die("M-Pesa STK Push failed: " . $response['message']);
    }
}

// Save info for thank you screen
$_SESSION['donor_name'] = $full_name;
$_SESSION['donated_amount'] = $amount;

if ($isNewUser) {
    $_SESSION['is_new_donor'] = true;
    $_SESSION['username_generated'] = $username;
}

// Redirect to thank you page
header("Location: ../Pages/thankyou.php");
exit();
?>
