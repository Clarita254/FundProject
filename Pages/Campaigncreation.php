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
    <h2 class="mb-4 text-center text-primary">Create New Campaign</h2>

    <form action="../Processes/Processcampaign.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="campaignTitle" class="form-label">Campaign Title</label>
                <input type="text" class="form-control" id="campaignTitle" name="campaignTitle" required>
            </div>
            <div class="col-md-6">
                <label for="campaignCategory" class="form-label">Category</label>
                <select class="form-select" id="campaignCategory" name="campaignCategory" required>
                    <option value="">-- Select Category --</option>
                    <option value="education">Education</option>
                    <option value="infrastructure">Infrastructure</option>
                    <option value="scholarship">Scholarship</option>
                    <option value="resources">Learning Resources</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="campaignDescription" class="form-label">Description</label>
            <textarea class="form-control" id="campaignDescription" name="campaignDescription" rows="4" required></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="targetAmount" class="form-label">Target Amount (KES)</label>
                <input type="number" class="form-control" id="targetAmount" name="targetAmount" min="100" required>
            </div>
            <div class="col-md-4">
                <label for="startDate" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="col-md-4">
                <label for="endDate" class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="campaignImage" class="form-label">Campaign Image</label>
                <input type="file" class="form-control" name="campaignImage" id="campaignImage" accept="image/*" required>
            </div>
            <div class="col-md-6">
                <label for="supportingDoc" class="form-label">Supporting Document (PDF/Image)</label>
                <input type="file" class="form-control" name="supportingDoc" id="supportingDoc" accept=".pdf,image/*" required>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">Submit Campaign</button>
        </div>
    </form>
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
