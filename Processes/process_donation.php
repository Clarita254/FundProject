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

// Check if user already exists in users table
$stmt = $conn->prepare("SELECT user_id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $user_id = $user['user_id'];
    $username = $user['username'];
} else {
    // Register user in users table
    $username = generateUsernameFromFullName($full_name);
    $hashedPassword = password_hash('default123', PASSWORD_DEFAULT);
    $role = 'donor';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, ?, TRUE)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Register donor profile in donors table
    $stmt = $conn->prepare("INSERT INTO donors (user_id, full_name, username, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $full_name, $username);
    $stmt->execute();
    $stmt->close();

    $isNewUser = true;
}

// Retrieve donor_Id from donors table using user_id
$stmt = $conn->prepare("SELECT donor_Id FROM donors WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$donorRow = $result->fetch_assoc();
$donor_Id = $donorRow['donor_Id'] ?? null;
$stmt->close();

if (!$donor_Id) {
    die("Error: Donor profile not found.");
}

// Insert into donations table using donor_Id from donors table
$stmt = $conn->prepare("INSERT INTO donations (donor_Id, campaign_id, amount, payment_mode, status, donation_date) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iidss", $donor_Id, $campaign_id, $amount, $payment_mode, $status);
$stmt->execute();
$donation_id = $stmt->insert_id;
$stmt->close();

// Start session for donor
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;
$_SESSION['role'] = 'donor';
$_SESSION['donation_Id'] = $donation_id;

if ($isNewUser) {
    $_SESSION['is_new_donor'] = true;
    $_SESSION['username_generated'] = $username;
}

// M-Pesa STK Push
if ($payment_mode === 'M-Pesa') {
    $response = initiateStkPush($conn, $phone, $amount, $donation_id);

    if (!$response['success']) {
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
