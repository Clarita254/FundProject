<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
require_once("../includes/db_connect.php");

// ✅ GET all campaigns
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM campaigns ORDER BY created_at DESC");

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => "Database error: " . $conn->error]);
        exit;
    }

    $campaigns = [];
    while ($row = $result->fetch_assoc()) {
        $campaigns[] = $row;
    }

    echo json_encode($campaigns);
    exit;
}

// ✅ POST new campaign with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔍 Debug logs
    error_log("---- POST DATA ----");
    error_log(print_r($_POST, true));
    error_log("---- FILES DATA ----");
    error_log(print_r($_FILES, true));

    $title = $_POST['campaignTitle'] ?? null;
    $description = $_POST['campaignDescription'] ?? null;
    $category = $_POST['campaignCategory'] ?? null;
    $goal = $_POST['targetAmount'] ?? null;
    $endDate = $_POST['endDate'] ?? null;
    $school_id = $_POST['school_id'] ?? null;
    $imagePath = null;

    if (!$title || !$description || !$goal || !$endDate || !$school_id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Handle image upload
    if (isset($_FILES['campaignImage']) && $_FILES['campaignImage']['error'] === 0) {
        $uploadsDir = "../uploads/";
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["campaignImage"]["name"]);
        $targetPath = $uploadsDir . $filename;

        if (move_uploaded_file($_FILES["campaignImage"]["tmp_name"], $targetPath)) {
            $imagePath = $filename;
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to upload image"]);
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO campaigns (title, description, category, target_amount, end_date, user_id, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssdsis", $title, $description, $category, $goal, $endDate, $school_id, $imagePath);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Campaign created successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Insert failed: " . $stmt->error]);
    }

    exit;
}

// Method not allowed
http_response_code(405);
echo json_encode(["error" => "Method Not Allowed"]);
exit;
?>
