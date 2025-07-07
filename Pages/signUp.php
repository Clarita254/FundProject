<?php
session_start();
require_once("../includes/db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!---Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!---Font Awesome---->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

     <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <title>User Sign Up</title>
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
        <input type="text" id="school_name">

      <label>Email</label>
      <input type="email" id="email" required>

      <label>Phone</label>
      <input type="tel" id="phone">
      

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

        <label>Rep Name</label>
        <input type="text" id="rep_name">

        <label>Rep Role</label>
        <select id="rep_role">
          <option value="">Select</option>
          <option>Headteacher</option>
          <option>Chancellor</option>
          <option>Accountant</option>
        </select>

        <label>Password</label>
      <input type="password" id="password" required>

      <label>Confirm Password</label>
      <input type="password" id="confirm_password" required>
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

  // Basic validation
  const username = document.getElementById("username").value.trim();
  const email = document.getElementById("email").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm_password").value;
  const role = document.getElementById("role").value;

  if (!username || !email || !password || !confirmPassword || !role) {
    alert("Please fill in all required fields.");
    return;
  }

  // Email format check
  const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!emailPattern.test(email)) {
    alert("Please enter a valid email address.");
    return;
  }

  // Phone validation (optional but must be valid if filled)
  if (phone && !/^\+?\d{7,15}$/.test(phone)) {
    alert("Please enter a valid phone number.");
    return;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match");
    return;
  }

  // If School Admin, check extra fields
  if (role === "schoolAdmin") {
    const requiredFields = [
      "school_name", "school_code", "school_type",
      "location", "county", "rep_name", "rep_role"
    ];
    for (const id of requiredFields) {
      const value = document.getElementById(id).value.trim();
      if (!value) {
        alert("Please fill in all school administrator fields.");
        return;
      }
    }
  }

  const payload = {
    username,
    email,
    phone,
    password,
    role
  };

  if (role === "schoolAdmin") {
    Object.assign(payload, {
      school_name: document.getElementById("school_name").value,
      school_code: document.getElementById("school_code").value,
      school_type: document.getElementById("school_type").value,
      location: document.getElementById("location").value,
      county: document.getElementById("county").value,
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
<?php