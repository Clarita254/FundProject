<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name = trim($_POST['school_name']);
    $school_code = trim($_POST['school_code']);
    $school_type = trim($_POST['school_type']);
    $location = trim($_POST['location']);
    $county = trim($_POST['county']);
    $date_established = $_POST['date_established'];
    $rep_name = trim($_POST['rep_name']);
    $rep_role = trim($_POST['rep_role']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $cleaned = strtolower(preg_replace("/[^a-zA-Z]/", "", $school_name));
    $randomNum = rand(100, 999);
    $username = $cleaned . "Admin" . $randomNum;

    $stmt = $conn->prepare("INSERT INTO users 
        (username, email, password, role, school_name, school_code, school_type, location, county, date_established, rep_name, rep_role, phone)
        VALUES (?, ?, ?, 'schoolAdmin', ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssssss", $username, $email, $hashedPassword, $school_name, $school_code, $school_type, $location, $county, $date_established, $rep_name, $rep_role, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully. Your username is: $username'); window.location.href='Campaign.php';</script>";
    } else {
        echo "<script>alert('Error creating account. Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
