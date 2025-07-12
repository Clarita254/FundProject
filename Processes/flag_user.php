

<?php //users can still log in
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "User ID not provided.";
    exit();
}

$user_id = intval($_GET['id']);

// Flag the user
$stmt = $conn->prepare("UPDATE users SET flagged = 1 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: ../Dashboards/systemAdminDashboard.php?flagged=success");
    exit();
} else {
    echo "Failed to flag user.";
}
?>
