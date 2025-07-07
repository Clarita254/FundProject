Fatal error: Uncaught mysqli_sql_exception: Unknown column 'supporting_doc_path' in 'field list' in C:\xampp\htdocs\EDUFUNDPROJECT\FundProject\Processes\Processcampaign.php:63 Stack trace: #0 C:\xampp\htdocs\EDUFUNDPROJECT\FundProject\Processes\Processcampaign.php(63): mysqli->prepare('INSERT INTO cam...') #1 {main} thrown in C:\xampp\htdocs\EDUFUNDPROJECT\FundProject\Processes\Processcampaign.php on line 63      <?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only schoolAdmin is logged in and form is submitted
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

    // Sanitize and collect campaign inputs
    $campaign_name = mysqli_real_escape_string($conn, $_POST['campaignTitle']);
    $description = mysqli_real_escape_string($conn, $_POST['campaignDescription']);
    $category = mysqli_real_escape_string($conn, $_POST['campaignCategory']);
    $target_amount = floatval($_POST['targetAmount']);
    $start_date = $_POST['startDate'];
    $end_date = $_POST['endDate'];

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

    // Upload supporting document
    $supporting_doc = null;
    if (isset($_FILES['supporting_doc']) && $_FILES['supporting_doc']['error'] === 0) {
        $doc_dir = "../uploads/supporting_docs/";
        if (!is_dir($doc_dir)) mkdir($doc_dir, 0777, true);

        $docname = uniqid() . '_' . basename($_FILES['supporting_doc']['name']);
        $doc_target = $doc_dir . $docname;

        if (move_uploaded_file($_FILES['supporting_doc']['tmp_name'], $doc_target)) {
            $supporting_doc= "..uploads/supporting_docs/" . $docname;
        }
    }

    // Insert campaign into the database
    $sql = "INSERT INTO campaigns (schoolAdmin_id, campaign_name, description, target_amount, start_date, end_date, category, image_path, supporting_doc, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issdsssss",
        $schoolAdminId,
        $campaign_name,
        $description,
        $target_amount,
        $start_date,
        $end_date,
        $category,
        $image_path,
        $supporting_doc
    );

   if ($stmt->execute()) {
    header("Location: ../Pages/ThankYouCampaign.php");
    exit();
} else {
    header("Location: ../Pages/Campaigncreation.php?error=" . urlencode($stmt->error));
    exit();
}

}
?>
