<?php
session_start();
require_once("../includes/db_connect.php");

// Only schoolAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Campaign - EduFund</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS + Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
</head>

<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container my-5">
    <div class="card shadow-lg border-0 rounded-4 px-4 py-5" style="background: linear-gradient(to right, #e3f2fd, #f1f8ff);">
       <div class="text-center mb-4">
    <h2 class="fw-bold text-white py-3 px-4 rounded text-center shadow"
        style="background: linear-gradient(to right, #0077cc, #00bfff); font-family:'Segoe UI', sans-serif; font-size: 2rem;">
        📘 Create New Campaign
    </h2>
</div>


        <form action="../Processes/Processcampaign.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="campaignTitle" class="form-label fw-semibold">Campaign Title</label>
                    <input type="text" class="form-control shadow-sm" id="campaignTitle" name="campaignTitle" required>
                </div>
                <div class="col-md-6">
                    <label for="campaignCategory" class="form-label fw-semibold">Category</label>
                    <select class="form-select shadow-sm" id="campaignCategory" name="campaignCategory" required>
                        <option value="">-- Select Category --</option>
                        <option value="education">Education</option>
                        <option value="infrastructure">Infrastructure</option>
                        <option value="scholarship">Scholarship</option>
                        <option value="resources">Learning Resources</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="campaignDescription" class="form-label fw-semibold">Description</label>
                <textarea class="form-control shadow-sm" id="campaignDescription" name="campaignDescription" rows="4" required></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="targetAmount" class="form-label fw-semibold">Target Amount (KES)</label>
                    <input type="number" class="form-control shadow-sm" id="targetAmount" name="targetAmount" min="100" required>
                </div>
                <div class="col-md-4">
                    <label for="startDate" class="form-label fw-semibold">Start Date</label>
                    <input type="date" class="form-control shadow-sm" id="startDate" name="startDate" required>
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label fw-semibold">End Date</label>
                    <input type="date" class="form-control shadow-sm" id="endDate" name="endDate" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="campaignImage" class="form-label fw-semibold">Campaign Image</label>
                    <input type="file" class="form-control shadow-sm" name="campaignImage" id="campaignImage" accept="image/*" required>
                </div>
                <div class="col-md-6">
                    <label for="supportingDoc" class="form-label fw-semibold">Upload Supporting Document <small>(PDF/Image - e.g. budget, actual needs)</small></label>
                    <input type="file" class="form-control shadow-sm" name="supportingDoc" id="supportingDoc" accept=".pdf,image/*" required>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-paper-plane me-1"></i> Submit Campaign
                </button>
            </div>
        </form>
    </div>
</div>


<?php include_once("../Templates/Footer.php"); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
    endDate.min = today;
});
</script>

</body>
</html>  