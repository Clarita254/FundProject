<?php
session_start();
require_once("../includes/db_connect.php");
// ========= THROTTLING BLOCK =========
$current_time = time();
if (!isset($_SESSION['signup_throttle'])) {
    $_SESSION['signup_throttle'] = [];
}
$attempts = &$_SESSION['signup_throttle'];

// Clear out old attempts (older than 10 minutes)
foreach ($attempts as $i => $timestamp) {
    if ($timestamp + 600 < $current_time) {
        unset($attempts[$i]);
    }
}

if (count($attempts) >= 3) {
    http_response_code(429);
    exit("Too many sign-up attempts. Please try again after 10 minutes.");
}

$attempts[] = $current_time;
// ========= END THROTTLING BLOCK =========

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title> Sign Up</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/SignUp.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<div class="signup-page">
  <div class="signup-container">
    <h2>Sign Up Form</h2>

    <form id="signupForm">


      <label>School Name</label>
      <input type="text" id="school_name" name="school_name" required placeholder="e.g. Nairobi West Primary">

      <label>Email (must be .edu domain)</label>
      <input type="email" id="email" name="email" required placeholder="e.g. info@nairobiwest.edu">

      <label>Phone (10 digits)</label>
      <input type="tel" id="phone" name="phone" required placeholder="e.g. 0712345678">

      <label>School Code(e.g 11000)</label>
      <input type="text" id="school_code" name="school_code" required>

      <label>School Type</label>
      <select id="school_type" name="school_type" required>
        <option value="">Select</option>
        <option value="Primary">Primary</option>
        <option value="Secondary">High school</option>
        <option value="University">University</option>
      </select>

      <label>Location <small class="text-muted">(e.g., Nairobi West)</small></label>
      <input type="text" id="location" name="location" required>

      <label>County</label>
      <input type="text" id="county" name="county" required placeholder="e.g. Nairobi">

      <label>Rep Name</label>
      <input type="text" id="rep_name" name="rep_name" required placeholder="e.g. John Mwangi">

      <label>Rep Role</label>
      <select id="rep_role" name="rep_role" required>
        <option value="">Select</option>
        <option value="Headteacher">Headmaster</option>
        <option value="Deputyteacher">DeputyPrincipal</option>
        <option value="Chancellor">Chancellor</option>
        <option value="Accountant">Accountant</option>
      </select>

      <label>Password</label>
      <input type="password" id="password" name="password" required placeholder="Min 8 chars with Upper, lower, number, symbol">

      <label>Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit" class="btn-submit">Sign Up</button>
    </form>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<script>
document.getElementById("signupForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;

  // Email validation
  const emailPattern = /^[^\s@]+@[^\s@]+\.edu$/;
  if (!emailPattern.test(email)) {
    alert("Please enter a valid .edu email without spaces.");
    return;
  }

  // Phone validation: 10 digits, no special characters
  const phonePattern = /^\d{10}$/;
  if (!phonePattern.test(phone)) {
    alert("Phone number must be exactly 10 digits (e.g., 0712345678), no spaces or symbols.");
    return;
  }

  // Password strength validation
  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/;
  if (!passwordPattern.test(password)) {
    alert("Password must be at least 8 characters long, with uppercase, lowercase, and a special character.");
    return;
  }

  // Confirm password check
  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    return;
  }

  // Prepare form data
  const formData = new FormData(e.target);
  const payload = Object.fromEntries(formData.entries());
  console.log("Payload:", payload);


  // Send via fetch to your API
  const res = await fetch("../api/signup.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });

  const data = await res.json();

  if (data.success) {
    alert("Signup successful!");
    window.location.href = "../Dashboards/schoolAdmindashboard.php";
  } else {
    alert(data.error || "Signup failed. Please try again.");
  }
});
</script>

</body>
</html>
