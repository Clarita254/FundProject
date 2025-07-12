<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
require_once("../includes/db_connect.php");

// 1. Get and decode JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid JSON data."]);
    exit;
}

// 2. Collect and sanitize input
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$school_name = trim($data['school_name'] ?? '');
$school_code = trim($data['school_code'] ?? '');
$school_type = trim($data['school_type'] ?? '');
$location = trim($data['location'] ?? '');
$county = trim($data['county'] ?? '');
$rep_name = trim($data['rep_name'] ?? '');
$rep_role = trim($data['rep_role'] ?? '');
$contact_email = $email;
$phone = trim($data['phone'] ?? '');

// 3. Check if email already exists
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email is already registered."]);
    exit;
}
$check->close();

// 4. Generate auto-username
$uniqueSuffix = rand(100000, 999999);
$generatedUsername = "schoolAdmin_" . $uniqueSuffix;

// 5. Insert into users table
$userStmt = $conn->prepare("INSERT INTO users (username, email, password, role, change_password) VALUES (?, ?, ?, 'schoolAdmin', true)");
$userStmt->bind_param("sss", $generatedUsername, $email, $hashedPassword);

if ($userStmt->execute()) {
    $user_id = $userStmt->insert_id;

    // 6. Insert into school_profiles table
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
        // Auto-login session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $generatedUsername;
        $_SESSION['role'] = 'schoolAdmin';
        $_SESSION['is_new_school_admin'] = true;

        echo json_encode(["success" => true]);
    } else {
        // Rollback user if profile fails
        $conn->query("DELETE FROM users WHERE user_id = $user_id");
        echo json_encode(["success" => false, "error" => "Failed to save school profile."]);
    }
    $schoolStmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Failed to register user."]);
}
$userStmt->close();
?>
