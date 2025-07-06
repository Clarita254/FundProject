<?php
session_start();
require_once('../includes/db_connect.php');
header('Content-Type: text/html; charset=UTF-8');

// Only allow logged-in donors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Handle password change via AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['new_password']) || !isset($data['confirm_password'])) {
        echo json_encode(['success' => false, 'message' => 'Missing password fields']);
        exit();
    }

    $newPassword = trim($data['new_password']);
    $confirmPassword = trim($data['confirm_password']);

    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit();
    }

    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET password = ?, change_password = 0 WHERE user_id = ?");
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/changepassword.css">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center mb-4 text-dark" style="color: #003366;">🔐 Change Your Password</h3>

    <div id="responseMessage" class="alert d-none" role="alert"></div>

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
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ new_password: newPassword, confirm_password: confirmPassword })
        })
        .then(res => res.json())
        .then(data => {
            messageBox.classList.remove('d-none', 'alert-success', 'alert-danger');
            messageBox.textContent = data.message;

            if (data.success) {
                messageBox.classList.add('alert-success');
                setTimeout(() => window.location.href = '../Dashboards/donorDashboard.php', 2000);
            } else {
                messageBox.classList.add('alert-danger');
            }
        })
        .catch(() => {
            messageBox.classList.remove('d-none');
            messageBox.classList.add('alert', 'alert-danger');
            messageBox.textContent = 'Something went wrong. Please try again.';
        });
    });
</script>
</body>
</html>
