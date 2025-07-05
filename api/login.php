<?php  
session_start();  // Start session to store user data

header("Content-Type: application/json");
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Basic validation
if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Username and password required"]);
    exit;
}

$username = $data['username'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        // ✅ Store session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        echo json_encode([
            "message" => "Login successful",
            "user_id" => $row['id'],
            "username" => $row['username'],
            "role" => $row['role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "User not found"]);
}
?>
