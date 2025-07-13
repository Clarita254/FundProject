<?php
session_start();
require_once('../includes/db_connect.php');

// ======= Throttling: max 3 change attempts per 10 minutes =======
$current_time = time();

if (!isset($_SESSION['change_password_throttle'])) {
    $_SESSION['change_password_throttle'] = [];
}

$requests = &$_SESSION['change_password_throttle'];

// Remove entries older than 10 minutes (600 seconds)
foreach ($requests as $i => $timestamp) {
    if ($timestamp + 600 < $current_time) {
        unset($requests[$i]);
    }
}

if (count($requests) >= 3) {
    http_response_code(429);
    exit("Too many password change attempts. Please try again after 1 minutes.");
}

// Log this attempt
$requests[] = $current_time;
// ======= End of throttling block =======


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Check if donor already changed password
$stmt = $conn->prepare("SELECT change_password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($changePassword);
$stmt->fetch();
$stmt->close();

if ($changePassword == 0) {
    header("Location: ../Dashboards/donorDashboard.php");
    exit();
}

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (strlen($newPassword) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "❌ Passwords do not match.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, change_password = 0 WHERE user_id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        if ($stmt->execute()) {
            unset($_SESSION['force_password_change']);
            $success = "✅ Password changed successfully. Redirecting to dashboard...";
            echo "<script>setTimeout(() => window.location.href = '../Pages/Donations.php', 2000);</script>";
        } else {
            $error = "❌ Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/changepassword.css">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <div class="card">
        <h4 class="text-center mb-4 text-primary">🔐 Change Your Password</h4>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Password</button>
        </form>
    </div>
</div>
</body>
</html>
