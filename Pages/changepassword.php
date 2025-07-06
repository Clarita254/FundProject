<?php
session_start();
require_once('../includes/db_connect.php');

header('Content-Type: text/html; charset=UTF-8');

// Allow only donors to access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['new_password']) || !isset($data['confirm_password'])) {
        echo json_encode(['success' => false, 'message' => 'Missing password fields']);
        exit();
    }

    $newPassword = trim($data['new_password']);
    $confirmPassword = trim($data['confirm_password']);

    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE user_id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    if ($stmt->execute()) {
        unset($_SESSION['force_password_change']);
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }
    exit();
}
?>

<!-- HTML (only visible on GET) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/changepassword.css">
</head>
<body>
<div class="container">
    <h3 class="text-center text-primary mb-4">🔐 Change Your Password</h3>

    <div id="responseMessage" class="alert" role="alert"></div>

    <form id="changePasswordForm">
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Password</button>
    </form>
</div>

<script>
    const form = document.getElementById('changePasswordForm');
    const messageBox = document.getElementById('responseMessage');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        fetch('change_password.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        })
        .then(res => res.json())
        .then(data => {
            messageBox.style.display = 'block';
            messageBox.textContent = data.message;
            messageBox.className = 'alert';

            if (data.success) {
                messageBox.classList.add('alert-success');
                setTimeout(() => {
                    window.location.href = '../Dashboards/donorDashboard.php';
                }, 2000);
            } else {
                messageBox.classList.add('alert-danger');
            }
        })
        .catch(err => {
            messageBox.style.display = 'block';
            messageBox.className = 'alert alert-danger';
            messageBox.textContent = 'Something went wrong.';
        });
    });
</script>
</body>
</html>
