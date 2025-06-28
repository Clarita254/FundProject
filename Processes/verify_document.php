<?php
require_once("../includes/db_connect.php");

if (isset($_GET['id'], $_GET['action'])) {
    $docId = intval($_GET['id']);
    $action = $_GET['action'];

    if (!in_array($action, ['approve', 'reject'])) {
        header("Location: ../Dashboards/systemAdminDashboard.php?error=InvalidDocumentAction");
        exit();
    }

    $status = $action === 'approve' ? 'Approved' : 'Rejected';

    $stmt = $conn->prepare("UPDATE verification_documents SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $docId);

    if ($stmt->execute()) {
        header("Location: ../Dashboards/systemAdminDashboard.php?success=DocumentUpdated");
    } else {
        header("Location: ../Dashboards/systemAdminDashboard.php?error=" . urlencode($stmt->error));
    }
    exit();
} else {
    header("Location: ../Dashboards/systemAdminDashboard.php?error=MissingParams");
    exit();
}
?>
