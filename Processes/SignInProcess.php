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
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Force password change only for new donors
            if ($user['role'] === 'donor' && $user['change_password']) {
                $_SESSION['force_password_change'] = true;
                header("Location: ../Pages/changepassword.php");
                exit();
            }

            // Redirect user based on their role
            switch ($user['role']) {
                case 'donor':
                    header("Location: ../Dashboards/donorDashboard.php");
                    break;
                case 'schoolAdmin':
                    header("Location: ../Dashboards/schoolAdmindashboard.php");
                    break;
                case 'systemAdmin':
                    header("Location: ../Dashboards/systemAdminDashboard.php");
                    break;
                default:
                    $_SESSION['error'] = "Unauthorized role.";
                    header("Location: ../Pages/signIn.php");
                    break;
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
