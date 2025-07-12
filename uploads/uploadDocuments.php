<?php
session_start();
require_once("../includes/db_connect.php");

// Only logged-in schoolAdmins can upload
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Handle the upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["verification_document"])) {
    $file = $_FILES["verification_document"];
    $uploadDir = "../uploads/verificationdocs/";

    // Create folder if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Validate file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Invalid file type. Only PDF, JPG, JPEG, PNG allowed.'); window.history.back();</script>";
        exit();
    }

    // Sanitize & create unique filename
    $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
    $filename = "verify_" . $_SESSION['user_id'] . "_" . time() . "_" . $safeName;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Store in DB
        $schoolAdminId = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO verification_documents (schoolAdmin_id, file_name, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $schoolAdminId, $filename);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Document uploaded successfully and is pending review.'); window.location.href='../Dashboards/schoolAdmindashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to move uploaded file.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No file uploaded. Please choose a file.'); window.history.back();</script>";
}
?>
