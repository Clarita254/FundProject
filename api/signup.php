<?php
session_start();

file_put_contents("signup_log.txt", date('Y-m-d H:i:s') . " - Signup.php hit\n", FILE_APPEND);


// Enable full error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// JSON response header
header("Content-Type: application/json");

require_once("../includes/db_connect.php");

// Capture raw JSON input for debugging
$input = file_get_contents("php://input");
file_put_contents("signup_log.txt", "Raw input: " . $input . PHP_EOL, FILE_APPEND);

file_put_contents("signup_debug.log", $input . PHP_EOL, FILE_APPEND);

// Decode JSON input
$data = json_decode($input, true);
if (!$data) {
    file_put_contents("signup_log.txt", "JSON decode failed\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Invalid JSON."]);
    exit;
}


if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON data."]);
    exit;
}

// Collect and sanitize inputs
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$school_name = trim($data['school_name'] ?? '');
$school_code = trim($data['school_code'] ?? '');
$school_type = trim($data['school_type'] ?? '');
$location = trim($data['location'] ?? '');
$county = trim($data['county'] ?? '');
$rep_name = trim($data['rep_name'] ?? '');
$rep_role = trim($data['rep_role'] ?? '');
$phone = trim($data['phone'] ?? '');

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$contact_email = $email;

// 1. Check if email already exists
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email is already registered."]);
    exit;
}
$check->close();

// 2. Generate unique auto-username
$schoolSlug = preg_replace('/[^a-zA-Z]/', '', strtolower($school_name));
$generatedUsername = substr($schoolSlug, 0, 10) . "Admin" . rand(1000, 9999);

// 3. Insert into users table
$userStmt = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, 'schoolAdmin', true)");
$userStmt->bind_param("sss", $generatedUsername, $email, $hashedPassword);

if ($userStmt->execute()) {
    $user_id = $userStmt->insert_id;

    // 4. Insert into school_profiles table
    $schoolStmt = $conn->prepare("INSERT INTO school_profiles 
        (schoolAdmin_id, school_name, contact_email, location, school_code, school_type, county, rep_role, rep_name, phone)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $schoolStmt->bind_param(
        "isssssssss",
        $user_id,
        $school_name,
        $contact_email,
        $location,
        $school_code,
        $school_type,
        $county,
        $rep_role,
        $rep_name,
        $phone
    );

    if ($schoolStmt->execute()) {
        // 5. Auto-login the new schoolAdmin
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $generatedUsername;
        $_SESSION['role'] = 'schoolAdmin';
        $_SESSION['is_new_school_admin'] = true;

        echo json_encode(["success" => true]);
    } else {
        // If school profile fails, rollback user
        $conn->query("DELETE FROM users WHERE user_id = $user_id");
        echo json_encode(["success" => false, "error" => "Failed to save school profile."]);
    }
    $schoolStmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Failed to register user."]);
}

$userStmt->close();
?>
