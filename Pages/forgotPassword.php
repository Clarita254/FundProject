<?php  //PHP mailer app password
session_start();
require_once("../includes/db_connect.php");

// ========== Throttling block ==========
$current_time = time();
if (!isset($_SESSION['forgot_password_throttle'])) {
    $_SESSION['forgot_password_throttle'] = [];
}
$attempts = &$_SESSION['forgot_password_throttle'];

// Clear out old timestamps (older than 1 minute = 60 seconds)
foreach ($attempts as $i => $timestamp) {
    if ($timestamp + 60 < $current_time) {
        unset($attempts[$i]);
    }
}

// Allow max 3 attempts per 60 seconds
if (count($attempts) >= 3) {
    http_response_code(429);
    exit("Too many password reset attempts. Please try again after 1 minute.");
    exit("Too many password reset attempts. Please try again in 1 minutes.");
}

$attempts[] = $current_time;
// ========== End Throttling ==========



// Load Composer's autoloader
require_once("../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $stmt->bind_result($userId);
        $stmt->fetch();

        $insert = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $userId, $token, $expires);
        $insert->execute();

        // Change this to your actual domain name and path
        $resetLink = "http://localhost/EDUFUNDPROJECT/FundProject/API/resetPassword.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'clara.wanjiru16@gmail.com'; // 
            $mail->Password = 'xfes bzkw mtvf xhbw';         //  PHP mailer app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('clara.wanjiru16@gmail.com', 'EduFund'); 
            $mail->addAddress($email);
            $mail->Subject = 'EduFund Password Reset Link';
            $mail->Body = "Hi,\n\nClick this link to reset your password:\n$resetLink\n\nLink expires in 1 hour.";

            $mail->send();
            $message = "A reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Email not found in our records.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
     <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
    <h4 class="mb-3 text-center">Forgot Your Password?</h4>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"> <?= $message ?> </div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
</div>
</body>
</html>
