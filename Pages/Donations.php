<?php
session_start();
require_once('../includes/db_connect.php');

$campaignId = $_GET['campaign_id'] ?? null;
if (!$campaignId) {
    die("Campaign not specified.");
}

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'guest';

$donorName = '';
$donorEmail = '';

if ($role === 'donor') {
    $stmt = $conn->prepare("SELECT d.full_name, u.email FROM donors d JOIN users u ON d.user_id = u.user_id WHERE d.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($donorName, $donorEmail);
    $stmt->fetch();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donate to Campaign</title>
  <link rel="stylesheet" href="../CSS/donations.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .donation-container {
      max-width: 600px;
      margin: auto;
      padding: 30px;
      background: #f7f9fc;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .btn-submit {
      background-color: #003366;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
    }
    .btn-submit:hover {
      background-color: #002244;
    }
  </style>
</head>
<body class="donation-page">

<?php include_once("../Templates/nav.php"); ?>

<div class="container mt-5">
  <h2 class="mb-4 text-center text-primary">Make a Donation</h2>

  <div class="donation-container">
    <form action="../Processes/process_donation.php" method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="campaign_id" value="<?= htmlspecialchars($campaignId) ?>">

      <?php if ($role !== 'donor'): ?>
        <!-- Guest donor fields -->
        <div class="mb-3">
          <label for="full_name" class="form-label">Full Name</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email (Gmail only)</label>
          <input type="email" name="email" class="form-control" required pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Email must end with @gmail.com">
        </div>
      <?php else: ?>
        <!-- Logged-in donor -->
        <input type="hidden" name="full_name" value="<?= htmlspecialchars($donorName) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($donorEmail) ?>">

        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($donorName) ?>" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($donorEmail) ?>" disabled>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label for="phone" class="form-label">Phone (2547XXXXXXXX)</label>
        <input type="text" name="phone" class="form-control" required pattern="2547[0-9]{8}">
      </div>

      <div class="mb-3">
        <label for="amount" class="form-label">Amount (KES)</label>
        <input type="number" name="amount" class="form-control" required min="10">
      </div>

      <div class="mb-3">
        <label for="payment_mode" class="form-label">Payment Mode</label>
        <select name="payment_mode" class="form-select" required>
          <option value="">-- Select Payment Mode --</option>
          <option value="M-Pesa">M-Pesa</option>
          <option value="Card">Card</option>
        </select>
      </div>

      <div class="text-end">
        <button type="submit" class="btn-submit">Donate Now</button>
      </div>
    </form>
  </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<script>
// Bootstrap validation
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
</body>
</html>

