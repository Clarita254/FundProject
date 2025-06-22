<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - EduFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/SignUp.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  


  <!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

 
</head>
<body>
    <!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="signup-card">
      <div class="text-center mb-4">
        <h2>Join EduFund</h2>
        <p class="text-muted small">Empowering schools through transparency</p>
      </div>

      <form action="processSignUp.php" method="POST">
        <div class="form-group mb-3">
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
        </div>

        <div class="form-group mb-3">
          <div class="input-icon">
            <i class="fas fa-envelope"></i>
            <input type="email" class="form-control" name="email" placeholder="Email" required>
          </div>
        </div>

        <div class="form-group mb-3">
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
        </div>

        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-signup">Create Account</button>
        </div>
      </form>

      <div class="text-center text-muted mb-3">or continue with</div>

      <div class="d-grid mb-3">
        <button class="btn google-btn">
          <img src="../Images/googleicon.png" alt="Google logo" class="google-icon">
          Sign up with Google
        </button>
      </div>

      <div class="text-center">
        <span>Already have an account?</span>
        <a href="signIn.php" class="btn btn-link">Sign In</a>
      </div>
    </div>
  </div>
    <!---Include footer---->
  <?php include_once("../Templates/Footer.php"); ?>

</body>
</html>