<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'schoolAdmin':
                    header("Location: ../Dashboards/schoolAdminDashboard.php");
                    break;
                case 'donor':
                    header("Location: ../Dashboards/donorDashboard.php");
                    break;
                case 'systemAdmin':
                    header("Location: ../Dashboards/systemAdminDashboard.php");
                    break;
                default:
                    echo "<script>alert('Unknown role. Access denied.'); window.location.href='signIn.php';</script>";
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='signIn.php';</script>";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='signIn.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
