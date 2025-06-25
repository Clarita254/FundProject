<?php
session_start();
require_once("../includes/db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'schoolAdmin') {
    $campaignId = intval($_POST['campaign_id']);
    $schoolAdminId = $_SESSION['user_id'];

    $title = mysqli_real_escape_string($conn, $_POST['campaignTitle']);
    $description = mysqli_real_escape_string($conn, $_POST['campaignDescription']);
    $targetAmount = floatval($_POST['targetAmount']);
    $endDate = $_POST['endDate'];

    // Optional: Check if this admin owns the campaign
    $checkQuery = "SELECT campaign_id FROM campaigns WHERE campaign_id = ? AND schoolAdmin_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $campaignId, $schoolAdminId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        die("Unauthorized or campaign not found.");
    }

    // Update
    $updateQuery = "UPDATE campaigns SET campaign_name = ?, description = ?, target_amount = ?, end_date = ? WHERE campaign_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssdsi", $title, $description, $targetAmount, $endDate, $campaignId);

    if ($stmt->execute()) {
        header("Location: ../Dashboards/manageCampaigns.php?update=success");
    } else {
        echo "Error updating: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: ../Pages/signIn.php");
}
?>
