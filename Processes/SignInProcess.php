<?php
require_once('../includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // === Validation ===
    if (strpos($username, ' ') !== false || strlen($password) < 8) {
        $_SESSION['error'] = "Username must not contain spaces and password must be at least 8 characters.";
        header("Location: ../Pages/signIn.php");
        exit();
    }

    // === Fetch user ===
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

            if ($user['role'] === 'donor' && $user['change_password']) {
                $_SESSION['force_password_change'] = true;
                header("Location: ../Pages/changepassword.php");
                exit();
            }

            switch ($user['role']) {
                case 'donor':
                    header("Location: ../Dashboards/donorDashboard.php"); break;
                case 'schoolAdmin':
                    header("Location: ../Dashboards/schoolAdmindashboard.php"); break;
                case 'systemAdmin':
                    header("Location: ../Dashboards/systemAdminDashboard.php"); break;
                default:
                    $_SESSION['error'] = "Unauthorized role.";
                    header("Location: ../Pages/signIn.php"); break;
            }
            exit();
        }
    }

    // If username not found or password invalid
    $_SESSION['error'] = "Invalid username or password. Please try again.";
    header("Location: ../Pages/signIn.php");
    exit();
}
