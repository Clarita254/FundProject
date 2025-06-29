<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only schoolAdmin is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && $_SESSION['role'] === 'schoolAdmin') {
    $schoolAdminId = $_SESSION['user_id'];

    // Check if the school is verified
    $verifyQuery = "SELECT status FROM verification_documents WHERE schoolAdmin_id = ? ORDER BY upload_time DESC LIMIT 1";
$verifyStmt = $conn->prepare($verifyQuery);
$verifyStmt->bind_param("i", $schoolAdminId);
$verifyStmt->execute();
$verifyStmt->bind_result($status);
$verifyStmt->fetch();
$verifyStmt->close();

if ($status !== 'Approved') {
    echo "<script>alert('Your school is not verified. Please upload verification documents.'); window.location.href='../Dashboards/schoolAdmindashboard.php';</script>";
    exit();
}


    // Continue with campaign creation
    $campaign_name = mysqli_real_escape_string($conn, $_POST['campaignTitle']);
    $description = mysqli_real_escape_string($conn, $_POST['campaignDescription']);
    $category = mysqli_real_escape_string($conn, $_POST['campaignCategory']);
    $target_amount = floatval($_POST['targetAmount']);
    $end_date = $_POST['endDate'];
    $start_date = date('Y-m-d');
    
    // Upload campaign image
    $image_path = null;
    if (isset($_FILES['campaignImage']) && $_FILES['campaignImage']['error'] === 0) {
        $upload_dir = "../uploads/campaigns/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = uniqid() . '_' . basename($_FILES['campaignImage']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['campaignImage']['tmp_name'], $target_file)) {
            $image_path = "campaigns/" . $filename; // relative to ../uploads/
        }
    }

    // Insert campaign into DB
    $sql = $sql = "INSERT INTO campaigns (schoolAdmin_id, campaign_name, description, target_amount, start_date, end_date, category, image_path, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
   $stmt->bind_param("issdssss", $schoolAdminId, $campaign_name, $description, $target_amount, $start_date, $end_date, $category, $image_path);


    if ($stmt->execute()) {
        header("Location../Pages/Campaign.php?success=1");
        exit();
    } else {
        header("Location: ../Pages/Campaigncreation.php?error=" . urlencode($stmt->error));
        exit();
    }

} else {
    header("Location: ../Pages/signIn.php");
    exit();
}
?>
