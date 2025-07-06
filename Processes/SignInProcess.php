<?php
require_once('../includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role, change_password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // ✅ Apply change password logic only to donors
            if ($user['role'] === 'donor' && $user['change_password']) {
                $_SESSION['force_password_change'] = true;
                header("Location: ../Pages/changepassword.php");
                exit();
            }

            // Redirect based on role
            if ($user['role'] === 'donor') {
                header("Location: ../Dashboards/donorDashboard.php");
            } elseif ($user['role'] === 'schoolAdmin') {
                header("Location: ../Dashboards/schoolAdmindashboard.php");
            } elseif ($user['role'] === 'systemAdmin') {
                header("Location: ../Dashboards/systemAdminDashboard.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password.";
        }
    } else {
        $_SESSION['error'] = "User not found.";
    }

    $stmt->close();
    header("Location: ../Pages/signIn.php");
    exit();
}
?>
