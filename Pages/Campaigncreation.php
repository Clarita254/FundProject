<?php
session_start();
require_once("../includes/db_connect.php");

// Only schoolAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

// Set breadcrumb title
$breadcrumbs = ["Create Campaign"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Campaign - EduFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
 
<link rel="stylesheet" href="../CSS/schoolAdminNav.css">
 
</head>

<body>
 <?php include("../Templates/nav.php"); ?>


<!-- Sidebar and breadcrumb -->
<?php include("../Templates/schoolAdminNav.php"); ?>






<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4 px-4 py-5" style="background: linear-gradient(to right, #e0f2ff, #f0f9ff);">
    <div class="text-center mb-4">
      <h2 class="fw-bold py-3 px-4 rounded shadow"
          style="background-color: #003366; color: white; font-family:'Segoe UI', sans-serif;">
        📘 Create New Campaign
      </h2>
    </div>

    <form id="campaignForm" action="../Processes/Processcampaign.php" method="POST" enctype="multipart/form-data">
      <div class="row mb-4">
        <div class="col-md-6">
          <label for="campaignTitle" class="form-label fw-semibold" style="color: #003366;">Campaign Title</label>
          <input type="text" class="form-control shadow-sm" style="border: 1px solid #003366;" id="campaignTitle" name="campaignTitle" required>
        </div>

        <div class="col-md-6">
          <label for="campaignCategory" class="form-label fw-semibold" style="color: #003366;">Category</label>
          <select class="form-select shadow-sm" style="border: 1px solid #003366;" id="campaignCategory" name="campaignCategory" required>
            <option value="">-- Select Category --</option>
            <option value="infrastructure">Infrastructure Development</option>
            <option value="learning">Learning Resources & Materials</option>
            <option value="feeding">School Feeding Programs</option>
            <option value="digital">Digital Learning & ICT</option>
            <option value="scholarship">Scholarships & Fees Support</option>
            <option value="special_needs">Special Needs Support</option>
            <option value="sanitation">Sanitation & Hygiene</option>
            <option value="emergency">Emergency or Disaster Relief</option>
            <option value="sports">Sports & Co-curricular</option>
            <option value="uniforms">School Uniforms & Clothing</option>
          </select>
        </div>
      </div>

      <div class="mb-4">
        <label for="campaignDescription" class="form-label fw-semibold" style="color: #003366;">Description</label>
        <textarea class="form-control shadow-sm" style="border: 1px solid #003366;" id="campaignDescription" name="campaignDescription" rows="4" required></textarea>
      </div>

      <div class="row mb-4">
        <div class="col-md-4">
          <label for="targetAmount" class="form-label fw-semibold" style="color: #003366;">Target Amount (KES)</label>
          <input type="number" class="form-control shadow-sm" style="border: 1px solid #003366;" id="targetAmount" name="targetAmount" min="100" required>
        </div>
        <div class="col-md-4">
          <label for="startDate" class="form-label fw-semibold" style="color: #003366;">Start Date</label>
          <input type="date" class="form-control shadow-sm" style="border: 1px solid #003366;" id="startDate" name="startDate" required>
        </div>
        <div class="col-md-4">
          <label for="endDate" class="form-label fw-semibold" style="color: #003366;">End Date</label>
          <input type="date" class="form-control shadow-sm" style="border: 1px solid #003366;" id="endDate" name="endDate" required>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
          <label for="campaignImage" class="form-label fw-semibold" style="color: #003366;">Campaign Image</label>
          <input type="file" class="form-control shadow-sm" style="border: 1px solid #003366;" name="campaignImage" id="campaignImage" accept="image/*" required>
        </div>
        <div class="col-md-6">
          <label for="supportingDoc" class="form-label fw-semibold" style="color: #003366;">
            Supporting Document <small class="text-muted">(PDF/Image e.g. Budget, Needs)</small>
          </label>
          <input type="file" class="form-control shadow-sm" style="border: 1px solid #003366;" name="supportingDoc" id="supportingDoc" accept=".pdf,image/*" required>
        </div>
      </div>

      <div class="text-end">
        <button type="submit" class="px-4 py-2 rounded-pill shadow-sm text-white border-0" id="submitBtn"
                style="background-color: #003366;">
          <i class="fas fa-paper-plane me-1"></i> Submit Campaign
        </button>
      </div>
    </form>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<!-- JavaScript: Bootstrap + Validation -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
    endDate.min = today;

    endDate.addEventListener("change", () => {
      if (endDate.value < startDate.value) {
        alert("End date cannot be before the start date.");
        endDate.value = "";
      }
    });

    document.getElementById("campaignForm").addEventListener("submit", function (e) {
      const confirmSubmit = confirm("Are you sure you want to submit this campaign?");
      if (!confirmSubmit) {
        e.preventDefault();
      }
    });
  });
</script>

</body>
</html>
