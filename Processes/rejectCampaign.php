<?php
require_once("../includes/db_connect.php");
if (!isset($_GET['id'])) {
    echo "Campaign ID missing.";
    exit();
}
$campaignId = intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reject Campaign</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <h3 class="mb-4">Reject Campaign</h3>
    <form action="verify_campaign.php?action=reject&id=<?= $campaignId ?>" method="POST">
        <div class="mb-3">
            <label for="review_comment" class="form-label">Reason for Rejection</label>
            <textarea name="review_comment" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Reject Campaign</button>
        <a href="../Dashboards/systemAdminDashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
