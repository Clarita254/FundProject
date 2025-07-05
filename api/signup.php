<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST method allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

if (!$username || !$password || !$role) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields: username, password, or role"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    if ($role === 'schoolAdmin') {
        $required = ['school_name', 'school_code', 'school_type', 'location', 'county', 'date_established', 'rep_name', 'rep_role', 'email'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing field: $field"]);
                exit;
            }
        }

        // ✅ Assign fields to variables first
        $school_name = $data['school_name'];
        $school_code = $data['school_code'];
        $school_type = $data['school_type'];
        $location = $data['location'];
        $county = $data['county'];
        $date_established = $data['date_established'];
        $rep_name = $data['rep_name'];
        $rep_role = $data['rep_role'];
        $email = $data['email'];
        $phone = $data['phone'] ?? null;

        $stmt = $conn->prepare("INSERT INTO users (
            username, password, role, school_name, school_code, school_type, location, county, 
            date_established, rep_name, rep_role, email, phone
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssssssss",
            $username,
            $hashedPassword,
            $role,
            $school_name,
            $school_code,
            $school_type,
            $location,
            $county,
            $date_established,
            $rep_name,
            $rep_role,
            $email,
            $phone
        );

    } elseif ($role === 'donor' || $role === 'systemAdmin') {
        $email = $data['email'];
        $phone = $data['phone'] ?? null;

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing field: email"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashedPassword, $role, $email, $phone);

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid role"]);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User registered successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $stmt->error]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
