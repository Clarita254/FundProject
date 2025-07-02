<?php
require_once('../includes/db_connect.php');
session_start();

function generateUsernameFromFullName($full_name) {
    $base = strtolower(str_replace(' ', '', $full_name));
    return $base . rand(1000, 9999); // e.g., marynjoki3478
}

// Collect POST data
$campaign_id = $_POST['campaign_id'] ?? null;
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$amount = (float)($_POST['amount'] ?? 0);
$payment_mode = $_POST['payment_mode'] ?? '';
$status = 'Completed';

// Validate
if (!$campaign_id || $amount <= 0 || empty($payment_mode) || empty($email) || empty($full_name)) {
    die("Invalid donation data.");
}

$isNewUser = false;

// Check if user exists
$stmt = $conn->prepare("SELECT user_id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    // User exists
    $donorId = $user['user_id'];
    $username = $user['username'];
} else {
    // Create new user
    $password = password_hash('default123', PASSWORD_DEFAULT);
    $username = generateUsernameFromFullName($full_name);
    $role = 'donor';

    $insertUser = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, ?, TRUE)");

    $insertUser->bind_param("ssss", $username, $email, $password, $role);
    $insertUser->execute();
    $donorId = $insertUser->insert_id;
    $insertUser->close();

    $isNewUser = true;
}

// Save donor_id in session (for logged-in donor)
$_SESSION['user_id'] = $donorId;
$_SESSION['role'] = 'donor';

// Insert donation
$insertDonation = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, payment_mode, status, donation_date) 
                                  VALUES (?, ?, ?, ?, ?, NOW())");
$insertDonation->bind_param("iidss", $donorId, $campaign_id, $amount, $payment_mode, $status);
$insertDonation->execute();
$insertDonation->close();

// Update campaign's total
$updateCampaign = $conn->prepare("UPDATE campaigns SET amount_raised = amount_raised + ? WHERE campaign_id = ?");
$updateCampaign->bind_param("di", $amount, $campaign_id);
$updateCampaign->execute();
$updateCampaign->close();

// Pass variables to thankyou page
$_SESSION['donor_name'] = $full_name;
$_SESSION['donated_amount'] = $amount;
if ($isNewUser) {
    $_SESSION['is_new_donor'] = true;
    $_SESSION['username_generated'] = $username;
}

header("Location: thankyou.php");
exit();
?>
