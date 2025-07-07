<?php
session_start();
require_once("../includes/db_connect.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'schoolAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

$schoolAdminId = $_SESSION['user_id'];
$campaignId = $_GET['campaign_id'] ?? null;

if (!$campaignId) {
    die("Invalid campaign ID.");
}

// Check if the campaign belongs to this schoolAdmin
$query = "SELECT image_path FROM campaigns WHERE campaign_id = ? AND schoolAdmin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $campaignId, $schoolAdminId);
$stmt->execute();
$result = $stmt->get_result();
$campaign = $result->fetch_assoc();
$stmt->close();

if (!$campaign) {
    die("You do not have permission to delete this campaign.");
}

// Delete campaign image
if (!empty($campaign['image_path'])) {
    $imageFile = "../uploads/" . $campaign['image_path'];
    if (file_exists($imageFile)) {
        unlink($imageFile);
    }
}

// Delete campaign
$deleteQuery = "DELETE FROM campaigns WHERE campaign_id = ? AND schoolAdmin_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $campaignId, $schoolAdminId);

if ($deleteStmt->execute()) {
    header("Location: ../Dashboards/manageCampaigns.php?deleted=1");
} else {
    echo "Error: " . $deleteStmt->error;
}
?>
