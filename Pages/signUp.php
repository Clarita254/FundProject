<?php
session_start();
require_once("../includes/db_connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>School Admin Sign Up</title>
  <link rel="stylesheet" href="../CSS/SignUp.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">

</head>
<body>
<!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>
<div class="signup-page">
  <div class="signup-container">
    <h2>School Admin Sign Up Form</h2>
    <form action="signUpProcess.php" method="POST">

      <label>School Name</label>
      <input type="text" name="school_name" required>

      <label>School Code / Reg No</label>
      <input type="text" name="school_code" required>

      <label>School Type</label>
      <select name="rep_role" required>
        <option value="">Select schoolType</option>
        <option value="Primary">Primary</option>
        <option value="Secondary">Secondary</option>
        <option value="University">University</option>
      </select>

      <label>School Location</label>
      <input type="text" name="location" required>

      <label>County</label>
      <input type="text" name="county" required>

      <label>Date of Establishment</label>
      <input type="date" name="date_established" required>

      <label>Representative Name</label>
      <input type="text" name="rep_name" required>

      <label>Representative Role</label>
      <select name="rep_role" required>
        <option value="">Select Role</option>
        <option value="Headteacher">Headteacher</option>
        <option value="Deputy Headteacher">Deputy Headteacher</option>
        <option value="Chancellor">Chancellor</option>
        <option value="Vice Chancellor">Vice Chancellor</option>
        <option value="Accountant">Accountant</option>

      </select>

      <label>SchoolEmail</label>
      <input type="email" name="email" required>

      <label>Phone Number</label>
      <input type="tel" name="phone">

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Confirm Password</label>
      <input type="password" name="confirm_password" required>

      <button type="submit" class="btn-submit">Create School Account</button>
    </form>
  </div>
</div>

  <?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
