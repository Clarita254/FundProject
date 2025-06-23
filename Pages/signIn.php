
<?php
session_start();
require_once("../includes/db_connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignIn-EduFund</title>

    <!---Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!---Font Awesome---->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

     <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/signIn.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  

  
</head>
<body>
<!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>
  
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="signin-card text-center shadown-sm">
        <div class="profile-icon mb-3">
            <i class="fas fa-user-circle"></i>
</div>

<h3 class="mb-3">Sign In</h3>

<form action="processSignIn.php" method="POST">
<!--Username-->
    <div class="form-group text-start mb-3">
        <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
        <input type="text"class="form-control"name="username" id="username" placeholder="Enter username"required>
</div>

<!---Password---->
<div class="form-group text-start mb-3">
        <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
        <input type="passsword"class="form-control"name="Password" id="password" placeholder="Enter password"required>
</div>


<!----Remember and Forgot Password----->

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="rememberMe">
        <label class="form-check-label" for="rememberMe">Remember me</label>
</div>
<a href=""class="forgot-password">Forgot Password?</a>
</div>


<!----Submit --->

<div class="d-grid">
    <button type="submit"class="btn btn-signin">Sign In</button>
</div>
</form>
</div>
</div>
   
<?php include_once("../Templates/Footer.php"); ?>

</body>
</html>