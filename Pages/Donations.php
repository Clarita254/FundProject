<?php
require_once("../includes/db_connect.php");
session_start();

if (isset($_GET['success'])) {
    echo '<div class="alert alert-success text-center fw-bold">' . htmlspecialchars($_GET['success']) . '</div>';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaignId = (int) $_POST['campaign_id'];
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $amount = (float) $_POST['amount'];
    $paymentMode = trim($_POST['payment_mode']);

    // Check if donor already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND role = 'donor'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Donor exists, log them in
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = 'donor';
    } else {
        // Donor does not exist, auto-register
        $defaultPassword = password_hash("donor123", PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'donor')");
        $insert->bind_param("sss", $fullName, $email, $defaultPassword);
        $insert->execute();
        $newUserId = $insert->insert_id;

        $_SESSION['user_id'] = $newUserId;
        $_SESSION['role'] = 'donor';
    }

    // Insert the donation
    $donorId = $_SESSION['user_id'];
    $status = 'Pending';
    $insertDonation = $conn->prepare("INSERT INTO donations (donor_id, campaign_id, amount, donation_date, payment_mode, status) VALUES (?, ?, ?, NOW(), ?, ?)");
    $insertDonation->bind_param("iisss", $donorId, $campaignId, $amount, $paymentMode, $status);

    if ($insertDonation->execute()) {
        header("Location: ../Pages/thankyou.php?donated=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error processing donation. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donate to Campaign</title>
  <link rel="stylesheet" href="../CSS/donations.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Make a Donation</h2>
  <form action="" method="POST">
    <input type="hidden" name="campaign_id" value="<?= htmlspecialchars($_GET['campaign_id'] ?? '') ?>">

    <div class="mb-3">
      <label for="full_name" class="form-label">Full Name</label>
      <input type="text" name="full_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="amount" class="form-label">Amount (KES)</label>
      <input type="number" name="amount" class="form-control" required min="10">
    </div>

    <div class="mb-3">
      <label for="payment_mode" class="form-label">Payment Mode</label>
      <select name="payment_mode" class="form-select" required>
        <option value="M-Pesa">M-Pesa</option>
        <option value="Bank">Bank</option>
        <option value="Card">Card</option>
      </select>
    </div>

    <button type="submit" class="btn btn-success">Donate Now</button>
  </form>
</div>
</body>
</html>
