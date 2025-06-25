<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only logged-in schoolAdmins can upload
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Handle the file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["verificationDoc"])) {
    $file = $_FILES["verificationDoc"];
    $uploadDir = "../uploads/verificationdocs/";

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Get file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

    // Validate file type
    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Invalid file type. Only PDF, JPG, PNG allowed.'); window.history.back();</script>";
        exit();
    }

    // Generate unique file name
    $filename = "verify_" . $_SESSION['user_id'] . "_" . time() . "." . $ext;
    $destination = $uploadDir . $filename;

    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Save file metadata into DB
        $schoolAdminId = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO verification_documents (schoolAdmin_id, file_name, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $schoolAdminId, $filename);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Verification document uploaded and recorded successfully.'); window.location.href='../Dashboards/schoolAdmindashboard.php';</script>";
    } else {
        echo "<script>alert('File upload failed. Please try again.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No file was uploaded.'); window.history.back();</script>";
}
?>
