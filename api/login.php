<?php  
session_start();  // Start session to store user data
header("Content-Type: application/json");

require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST requests are allowed"]);
    exit;
}

// Read and decode incoming JSON
$data = json_decode(file_get_contents("php://input"), true);

// Basic presence check
if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Username and password are required"]);
    exit;
}

$username = trim($data['username']);
$password = $data['password'];

// Custom validation
if (strpos($username, ' ') !== false || strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Username must not contain spaces and password must be at least 8 characters"]);
    exit;
}

// Check user in database
$stmt = $conn->prepare("SELECT id, username, password, role, change_password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        // Valid login — set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // If donor needs to change password
        $forceChange = false;
        if ($row['role'] === 'donor' && $row['change_password']) {
            $_SESSION['force_password_change'] = true;
            $forceChange = true;
        }

        echo json_encode([
            "message" => "Login successful",
            "user_id" => $row['id'],
            "username" => $row['username'],
            "role" => $row['role'],
            "force_password_change" => $forceChange
        ]);
        exit;
    }
}

// If no match or incorrect password
http_response_code(401);
echo json_encode(["error" => "Invalid username or password"]);
exit;
?>
