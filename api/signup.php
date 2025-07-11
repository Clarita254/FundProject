<?php
session_start();
require_once("../includes/db_connect.php");

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Only POST method allowed"]);
    exit;
}

// Get raw POST body and decode JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// Extract and validate required fields
$requiredFields = [
    'school_name', 'school_code', 'school_type', 'location', 'county',
    'rep_name', 'rep_role', 'email', 'phone', 'password', 'confirm_password'
];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit;
    }
}

if ($data['password'] !== $data['confirm_password']) {
    http_response_code(400);
    echo json_encode(["error" => "Passwords do not match."]);
    exit;
}

// Hash password
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Generate username
$cleaned = strtolower(preg_replace("/[^a-zA-Z]/", "", $data['school_name']));
$randomNum = rand(100, 999);
$username = $cleaned . "Admin" . $randomNum;

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (
    username, email, password, role, school_name, school_code, school_type, location, county, date_established, rep_name, rep_role, phone
) VALUES (?, ?, ?, 'schoolAdmin', ?, ?, ?, ?, ?, NOW(), ?, ?, ?)");

$stmt->bind_param("sssssssssss",
    $username,
    $data['email'],
    $hashedPassword,
    $data['school_name'],
    $data['school_code'],
    $data['school_type'],
    $data['location'],
    $data['county'],
    $data['rep_name'],
    $data['rep_role'],
    $data['phone']
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "username" => $username]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error creating account. Please try again."]);
}

$stmt->close();
$conn->close();
?>
