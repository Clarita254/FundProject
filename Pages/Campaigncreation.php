<?php
require_once("../includes/db_connect.php");

// Optional: Display error if redirected back
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($_GET['error']) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign - EduFund</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="../CSS/Campaign.css">
    <link rel="stylesheet" href="../CSS/Footer.css">
    <link rel="stylesheet" href="../CSS/navbar.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once("../Templates/nav.php"); ?>

    <div class="main">
        <h1 class="page-title">CREATE CAMPAIGN</h1>

        <!-- Back to campaigns list -->
        <div class="mb-3">
            <a href="campaign.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back to Campaigns</a>
        </div>

        <!-- Campaign Form -->
        <div class="card card-body">
            <h3 class="mb-4">Start a New Campaign</h3>
            <form id="campaignForm" action="process_campaign.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="campaignTitle" class="form-label">Campaign Title</label>
                        <input type="text" class="form-control" id="campaignTitle" name="campaignTitle" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="campaignCategory" class="form-label">Category</label>
                        <select class="form-select" id="campaignCategory" name="campaignCategory" required>
                            <option value="">Select category</option>
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

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="targetAmount" class="form-label">Target Amount ($)</label>
                        <input type="number" class="form-control" id="targetAmount" name="targetAmount" min="100" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="campaignImage" class="form-label">Campaign Image</label>
                        <input type="file" class="form-control" id="campaignImage" name="campaignImage" accept="image/*">
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">Clear</button>
                    <button type="submit" class="btn btn-success">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../Templates/Footer.php'; ?>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endDateField = document.getElementById('endDate');
            if (endDateField) {
                endDateField.min = new Date().toISOString().split('T')[0];
            }

            const campaignForm = document.getElementById('campaignForm');
            if (campaignForm) {
                campaignForm.addEventListener('submit', function (e) {
                    const targetAmount = document.getElementById('targetAmount').value;
                    if (targetAmount < 100) {
                        alert('Minimum target amount is $100');
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
