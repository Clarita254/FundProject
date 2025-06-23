<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'schoolAdmin') {
                header("Location: ../Pages/campaignForm.php");
            } elseif ($user['role'] === 'donor') {
                header("Location: ../Pages/donorDashboard.php");
            } elseif ($user['role'] === 'systemAdmin') {
                header("Location: ../Pages/adminDashboard.php");
            } else {
                echo "<script>alert('Unknown user role.');</script>";
            }
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location.href='signIn.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with that email.'); window.location.href='signIn.php';</script>";
    }
}
?>
