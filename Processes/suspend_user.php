<?php //prevents suspended users from accessing the account
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

// Suspend the user
$stmt = $conn->prepare("UPDATE users SET suspended = 1 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: ../Dashboards/systemAdminDashboard.php?suspended=success");
    exit();
} else {
    echo "Failed to suspend user.";
}
?>
