<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only systemAdmin accesses this file
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("Missing parameters.");
}

$docId = intval($_GET['id']);
$action = $_GET['action'];

if (!in_array($action, ['approve', 'reject'])) {
    die("Invalid action.");
}

$status = $action === 'approve' ? 'Approved' : 'Rejected';

// Update document status
$stmt = $conn->prepare("UPDATE verification_documents SET status = ?, notified = 0 WHERE id = ?");
$stmt->bind_param("si", $status, $docId);

if ($stmt->execute()) {
    header("Location: ../Dashboards/systemAdmindashboard.php?message=Document+{$status}");
    exit();
} else {
    echo "Failed to update document.";
}
?>
