<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check user found
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'schoolAdmin') {
                header("Location: ../Pages/Campaign.php");
            } elseif ($user['role'] === 'donor') {
                header("Location: ../Pages/donorDashboard.php");
            } elseif ($user['role'] === 'systemAdmin') {
                header("Location: ../Pages/adminDashboard.php");
            } else {
                echo "<script>alert('Role not recognized.'); window.location.href='signIn.php';</script>";
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Username not found.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
