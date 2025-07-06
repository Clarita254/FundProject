<?php 
header("Content-Type: application/json");
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST allowed"]);
    exit;
}

if (!isset($_FILES['document']) || !isset($_POST['school_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing file or school_id"]);
    exit;
}

$school_id = $_POST['school_id'];
$file = $_FILES['document'];
$file_name = basename($file['name']);
$temp_path = $file['tmp_name'];

$target_dir = "../uploads/";
$target_path = $target_dir . $file_name;

// Move uploaded file
if (move_uploaded_file($temp_path, $target_path)) {
    $stmt = $conn->prepare("INSERT INTO verification_documents (schoolAdmin_id, file_name, file_path, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iss", $school_id, $file_name, $target_path);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "File uploaded and pending approval"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database insert failed"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "File upload failed"]);
}
?>
