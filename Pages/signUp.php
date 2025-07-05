<?php
session_start();
require_once("../includes/db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Sign Up</title>
  <link rel="stylesheet" href="../CSS/SignUp.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="signup-page">
  <div class="signup-container">
    <h2>User Sign Up Form</h2>

    <form id="signupForm">
      <label>Username</label>
      <input type="text" id="username" required>

      <label>Email</label>
      <input type="email" id="email" required>

      <label>Phone</label>
      <input type="tel" id="phone">

      <label>Password</label>
      <input type="password" id="password" required>

      <label>Confirm Password</label>
      <input type="password" id="confirm_password" required>

      <label>Role</label>
      <select id="role" required>
        <option value="">Select Role</option>
        <option value="schoolAdmin">School Admin</option>
        <option value="donor">Donor</option>
        <option value="systemAdmin">System Admin</option>
      </select>

      <!-- 🏫 SCHOOL ADMIN EXTRA FIELDS -->
      <div id="schoolFields" style="display: none;">
        <label>School Name</label>
        <input type="text" id="school_name">

        <label>School Code</label>
        <input type="text" id="school_code">

        <label>School Type</label>
        <select id="school_type">
          <option value="">Select</option>
          <option>Primary</option>
          <option>Secondary</option>
          <option>University</option>
        </select>

        <label>Location</label>
        <input type="text" id="location">

        <label>County</label>
        <input type="text" id="county">

        <label>Date Established</label>
        <input type="date" id="date_established">

        <label>Rep Name</label>
        <input type="text" id="rep_name">

        <label>Rep Role</label>
        <select id="rep_role">
          <option value="">Select</option>
          <option>Headteacher</option>
          <option>Chancellor</option>
          <option>Accountant</option>
        </select>
      </div>

      <button type="submit" class="btn-submit">Sign Up</button>
    </form>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<script>
document.getElementById("role").addEventListener("change", (e) => {
  const role = e.target.value;
  document.getElementById("schoolFields").style.display = role === "schoolAdmin" ? "block" : "none";
});

document.getElementById("signupForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const role = document.getElementById("role").value;
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;

  if (password !== confirmPassword) {
    alert("Passwords do not match");
    return;
  }

  const payload = {
    username: document.getElementById("username").value,
    email: document.getElementById("email").value,
    phone: document.getElementById("phone").value,
    password: password,
    role: role
  };

  if (role === "schoolAdmin") {
    Object.assign(payload, {
      school_name: document.getElementById("school_name").value,
      school_code: document.getElementById("school_code").value,
      school_type: document.getElementById("school_type").value,
      location: document.getElementById("location").value,
      county: document.getElementById("county").value,
      date_established: document.getElementById("date_established").value,
      rep_name: document.getElementById("rep_name").value,
      rep_role: document.getElementById("rep_role").value
    });
  }

  const res = await fetch("/FundProject/api/signup.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const data = await res.json();
  if (data.success) {
    alert("Signup successful!");
    window.location.href = "signIn.php";
  } else {
    alert(data.error || "Signup failed");
  }
});
</script>

</body>
</html>
