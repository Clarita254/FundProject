<?php
session_start();
require_once("../includes/db_connect.php");

// Only allow systemAdmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "User ID not provided.";
    exit();
}

$user_id = intval($_GET['id']);

// Unflag the user in suspicious_activity
$stmt = $conn->prepare("UPDATE suspicious_activity SET is_flagged = 0 WHERE user_id = ?");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: ../Dashboards/systemAdminDashboard.php?unflagged=success");
    exit();
} else {
    echo "Failed to unflag user.";
}
?>
