<?php
session_start();
require_once('../includes/db_connect.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['force_password_change'])) {
    header("Location: ../Pages/signIn.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, must_change_password = FALSE WHERE user_id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();
        $stmt->close();

        unset($_SESSION['force_password_change']);

        header("Location: donor_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Change Your Password</h3>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
</body>
</html>
