<?php
session_start();

$donorName = $_SESSION['donor_name'] ?? 'Donor';
$donatedAmount = $_SESSION['donated_amount'] ?? '0.00';
$isNew = $_SESSION['is_new_donor'] ?? false;
$username = $_SESSION['username_generated'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="thankyou-page">
  <div class="thankyou-container">
    <h2>Thank You, <?= htmlspecialchars($donorName) ?>!</h2>
    <p>Your donation of <strong>KES <?= number_format((float)$donatedAmount, 2) ?></strong> was successful.</p>

    <?php if ($isNew && $username): ?>
      <div class="alert alert-info">
        <p>Welcome, <?= htmlspecialchars($donorName) ?>!<br>
        Your username is: <strong><?= htmlspecialchars($username) ?></strong><br>
        Your default password is: <strong>default123</strong><br>
        Please log in and change your password.</p>
      </div>
    <?php endif; ?>

    <a href="donor_dashboard.php" class="btn btn-primary mt-3">Go to Dashboard</a>
  </div>
</body>

</html>

<?php
// Clear the session messages
unset($_SESSION['donor_name'], $_SESSION['donated_amount'], $_SESSION['is_new_donor'], $_SESSION['username_generated']);
?>
