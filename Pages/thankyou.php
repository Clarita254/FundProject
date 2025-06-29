<?php
session_start();
$donorName = $_SESSION['username'] ?? 'Valued Donor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You - EduFund</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>
<body>

<div class="thankyou-card">
    <div class="thankyou-icon mb-3">
        <i class="fas fa-check-circle"></i>
    </div>
    <h2 class="text-success fw-bold">Thank You, <?= htmlspecialchars($donorName) ?>! 🎉</h2>
    <p class="mt-3 fs-5">Your generous donation has been received successfully. <br>
    Your support helps under-resourced schools get closer to achieving their goals.</p>

    <a href="../Pages/Campaign.php" class="btn btn-primary btn-home">
        <i class="fas fa-arrow-left me-2"></i>Back to Campaigns
    </a>
    <a href="../Dashboards/donorDashboard.php" class="btn btn-outline-success btn-home">
        <i class="fas fa-user me-2"></i>Go to Donor Dashboard
    </a>
</div>

</body>
</html>
