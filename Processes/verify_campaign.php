<?php
require_once("../includes/db_connect.php");

if (isset($_GET['id'], $_GET['action'])) {
    $campaignId = intval($_GET['id']);
    $action = $_GET['action'];

    // Approve action
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE campaigns SET status = 'Approved', review_comment = NULL WHERE campaign_id = ?");
        $stmt->bind_param("i", $campaignId);

    // Reject action — only via POST
    } elseif ($action === 'reject' && $_SERVER["REQUEST_METHOD"] === "POST") {
        $comment = trim($_POST['review_comment'] ?? '');

        if (empty($comment)) {
            header("Location: ../Dashboards/systemAdminDashboard.php?error=EmptyComment");
            exit();
        }

        $stmt = $conn->prepare("UPDATE campaigns SET status = 'Rejected', review_comment = ? WHERE campaign_id = ?");
        $stmt->bind_param("si", $comment, $campaignId);

    } else {
        header("Location: ../Dashboards/systemAdminDashboard.php?error=InvalidAction");
        exit();
    }

    if ($stmt->execute()) {
        header("Location: ../Dashboards/systemAdminDashboard.php?success=CampaignUpdated");
        exit();
    } else {
        header("Location: ../Dashboards/systemAdminDashboard.php?error=" . urlencode($stmt->error));
        exit();
    }
} else {
    header("Location: ../Dashboards/systemAdminDashboard.php?error=MissingParams");
    exit();
}
?>
