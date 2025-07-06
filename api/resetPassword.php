<?php
session_start();
require_once("../includes/db_connect.php");

$token = $_GET['token'] ?? '';
$message = '';
$showForm = false;
$success = false;

// Validate token and expiration
if (!empty($token)) {
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $expires_at);
        $stmt->fetch();

        if (strtotime($expires_at) >= time()) {
            $showForm = true;
        } else {
            $message = "This reset link has expired.";
        }
    } else {
        $message = "Invalid or expired reset link.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'], $_POST['token'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $formToken = $_POST['token'];

    if ($newPassword !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $formToken);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId);
            $stmt->fetch();

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $hashedPassword, $userId);
            $update->execute();

            $delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete->bind_param("s", $formToken);
            $delete->execute();

            $success = true;
            $showForm = false;
        } else {
            $message = "Invalid token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - EduFund</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/resetpassword.css">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">

<div class="reset-card text-start">
  <?php if ($success): ?>
    <div class="success-animation">
      <i class="fas fa-check-circle mb-3"></i>
      <h4>Password Reset Successful</h4>
      <p>You will be redirected to login shortly.</p>
      <p class="countdown">Redirecting in <span id="countdown">5</span> seconds...</p>
    </div>
    <script>
      let seconds = 5;
      const countdown = document.getElementById("countdown");
      const interval = setInterval(() => {
        seconds--;
        countdown.textContent = seconds;
        if (seconds <= 0) {
          clearInterval(interval);
          window.location.href = "../Pages/signIn.php";
        }
      }, 1000);
    </script>
  <?php else: ?>
    <h4 class="mb-3 text-center"><i class="fas fa-unlock-alt me-2"></i>Reset Password</h4>

    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>


     <form method="POST" novalidate>
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

  <!-- New Password -->
  <div class="mb-3 password-wrapper">
    <label for="new_password" class="form-label">New Password</label>
    <input type="password" class="form-control pe-5" name="new_password" id="new_password" required>
    <i class="fas fa-eye toggle-eye" id="toggleNewPassword"></i>
    <div id="strengthMessage" class="mt-2 small"></div>
    <div class="progress mt-1" style="height: 6px;">
      <div class="progress-bar" id="strengthBar"></div>
    </div>
    <ul class="mt-2 small text-muted ps-3 mb-0">
      <li>At least 8 characters</li>
      <li>Contains uppercase & lowercase</li>
      <li>At least 1 number & symbol</li>
    </ul>
  </div>

  <!-- Confirm Password -->
  <div class="mb-3 password-wrapper">
    <label for="confirm_password" class="form-label">Confirm Password</label>
    <input type="password" class="form-control pe-5" name="confirm_password" id="confirm_password" required>
    <i class="fas fa-eye toggle-eye" id="toggleConfirmPassword"></i>
    <div id="matchMessage" class="mt-1 small text-muted"></div>
  </div>

  <button type="submit" class="btn btn-primary w-100">Reset Password</button>
</form>

    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
  const newPassword = document.getElementById("new_password");
  const confirmPassword = document.getElementById("confirm_password");
  const strengthBar = document.getElementById("strengthBar");
  const strengthMessage = document.getElementById("strengthMessage");
  const matchMessage = document.getElementById("matchMessage");

  document.getElementById("toggleNewPassword").addEventListener("click", () => {
    const type = newPassword.type === "password" ? "text" : "password";
    newPassword.type = type;
    toggleNewPassword.classList.toggle("fa-eye-slash", type === "text");
  });

  document.getElementById("toggleConfirmPassword").addEventListener("click", () => {
    const type = confirmPassword.type === "password" ? "text" : "password";
    confirmPassword.type = type;
    toggleConfirmPassword.classList.toggle("fa-eye-slash", type === "text");
  });

  newPassword.addEventListener("input", () => {
    const val = newPassword.value;
    let strength = 0;

    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) strength++;
    if (/\d/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const colors = ['#dc3545', '#ffc107', '#28a745'];
    const messages = ['Weak \u{1F534}', 'Moderate \u{1F7E1}', 'Strong \u{1F7E2}'];

    strengthBar.style.width = `${(strength / 4) * 100}%`;
    strengthBar.style.backgroundColor = colors[strength - 1] || '#ccc';
    strengthMessage.textContent = messages[strength - 1] || 'Too short';
    strengthMessage.style.color = colors[strength - 1] || '#999';
  });

  confirmPassword.addEventListener("input", () => {
    if (confirmPassword.value !== newPassword.value) {
      matchMessage.textContent = "Passwords do not match ❌";
      matchMessage.style.color = "red";
    } else {
      matchMessage.textContent = "Passwords match ✅";
      matchMessage.style.color = "green";
    }
  });

  document.getElementById("generatePassword").addEventListener("click", () => {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
    let password = "";
    for (let i = 0; i < 12; i++) {
      password += charset[Math.floor(Math.random() * charset.length)];
    }
    newPassword.value = password;
    confirmPassword.value = password;
    newPassword.dispatchEvent(new Event("input"));
    confirmPassword.dispatchEvent(new Event("input"));
  });
</script>

</body>
</html>
