<?php
require_once("../includes/db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['campaignTitle']);
    $description = mysqli_real_escape_string($conn, $_POST['campaignDescription']);
    $category = mysqli_real_escape_string($conn, $_POST['campaignCategory']);
    $target_amount = mysqli_real_escape_string($conn, $_POST['targetAmount']);
    $end_date = mysqli_real_escape_string($conn, $_POST['endDate']);
    
    // Handle file upload
    $image = null;
    if (isset($_FILES['campaignImage']) && $_FILES['campaignImage']['error'] == 0) {
        $target_dir = "../uploads/campaigns/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['campaignImage']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $filename;
        
        $check = getimagesize($_FILES['campaignImage']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['campaignImage']['tmp_name'], $target_file)) {
                $image = $filename;
            }
        }
    }
    
    // Insert into database
    $query = "INSERT INTO campaigns (title, description, category, target_amount, end_date, image, user_id) 
              VALUES ('$title', '$description', '$category', $target_amount, '$end_date', '$image', 1)";
    
    if (mysqli_query($conn, $query)) {
        header("Location: campaign.php?success=1");
        exit();
    } else {
        header("Location: campaign.php?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    header("Location: campaign.php");
    exit();
}
?>
