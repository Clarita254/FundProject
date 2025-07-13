
<?php
session_start();
require_once("../includes/db_connect.php");

// ========= THROTTLING BLOCK =========
$current_time = time();
if (!isset($_SESSION['signin_throttle'])) {
    $_SESSION['signin_throttle'] = [];
}
$attempts = &$_SESSION['signin_throttle'];

foreach ($attempts as $i => $timestamp) {
    if ($timestamp + 60 < $current_time) {
        unset($attempts[$i]);
    }
}

// Block if 3 or more attempts in the last 60 seconds
if (count($attempts) >= 3) {
    http_response_code(429);
    exit("Too many sign-in attempts. Please try again after 1 minute.");
}

$attempts[] = $current_time;
// ========= END THROTTLING =========

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SignIn - EduFund</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/signIn.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="signin-page">
  <div class="signin-card text-center shadow-sm">
    <div class="profile-icon mb-3">
      <i class="fas fa-user-circle fa-3x"></i>
    </div>
    <h3 class="mb-3">Sign In</h3>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger text-center">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="../Processes/SignInProcess.php" method="POST" novalidate>
      <!-- Username -->
      <div class="form-group text-start mb-3">
        <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
        <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required pattern="^\S+$" title="Username must not contain spaces">
        <div class="invalid-feedback">Username must not contain spaces.</div>
      </div>

      <!-- Password -->
      <div class="form-group text-start mb-3 position-relative">
        <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required minlength="8" title="Password must be at least 8 characters long">
        <div class="invalid-feedback">Password must be at least 8 characters long.</div>
        <i class="fas fa-eye password-toggle" style="position:absolute; top: 50%; right: 12px; transform: translateY(-50%); cursor:pointer;"></i>
      </div>

      <!-- Remember & Forgot -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="rememberMe">
          <label class="form-check-label" for="rememberMe">Remember me</label>
        </div>
        <a href="../Pages/forgotPassword.php" class="forgot-password">Forgot Password?</a>
      </div>

      <!-- Submit -->
      <div class="d-grid">
        <button type="submit" class="btn btn-signin">Sign In</button>
      </div>
    </form>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelector('.signin-card').classList.add('show');

  const passwordField = document.getElementById("password");
  const toggleIcon = document.querySelector(".password-toggle");

  toggleIcon.addEventListener("click", () => {
    const isPassword = passwordField.getAttribute("type") === "password";
    passwordField.setAttribute("type", isPassword ? "text" : "password");
    toggleIcon.classList.toggle("fa-eye-slash", isPassword);
  });
});

// Form validation
(() => {
  'use strict';
  const form = document.querySelector('form');
  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>

</body>
</html>
