<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name = trim($_POST['school_name']);
    $email = trim($_POST['email']);
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Generate unique username
    $cleaned = strtolower(preg_replace("/[^a-zA-Z]/", "", $school_name));
    $randomNum = rand(100, 999);
    $username = $cleaned . "Admin" . $randomNum;

    // Role assignment
    $role = "schoolAdmin";

    // Insert user into DB
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        // Get the inserted user ID
        $userId = $conn->insert_Id;

        // Store session data
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect to campaign creation page
        header("Location: campaignForm.php");
        exit();
    } else {
        echo "<script>alert('Error creating account. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
