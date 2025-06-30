<?php
header("Content-Type: application/json");
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST allowed"]);
    exit;
}

$school_id = $_POST['school_id'];
$file_name = $_FILES['document']['name'];
$temp_path = $_FILES['document']['tmp_name'];
$target_dir = "../uploads/";
$target_path = $target_dir . basename($file_name);

if (move_uploaded_file($temp_path, $target_path)) {
    $stmt = $conn->prepare("INSERT INTO documents (school_id, file_path, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("is", $school_id, $target_path);
    $stmt->execute();

    echo json_encode(["message" => "File uploaded and pending approval"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "File upload failed"]);
}
?>
