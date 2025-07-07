<?php
session_start();
require_once("../includes/db_connect.php");

// Restrict access to logged-in schoolAdmins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Campaign Submitted - EduFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/Home.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<!-- Thank You Section -->
<section class="hero d-flex align-items-center justify-content-center text-center text-white" style="height: 70vh;">
  <div>
    <h1 class="display-4 fw-bold mb-3">🎉 Campaign Submitted!</h1>
    <p class="lead mb-4">Thank you for submitting your campaign.We will review your submission shortly.</p>
    <div class="d-flex justify-content-center gap-3">
      <a href="../Dashboards/schoolAdmindashboard.php" class="btn btn-outline-light btn-lg">
        <i class="fas fa-home me-2"></i> Go to Dashboard
      </a>
    </div>
  </div>
</section>

<?php include_once("../Templates/Footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
