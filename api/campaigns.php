<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
require_once("../includes/db_connect.php");

// ✅ GET - Filter & return approved campaigns
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $goalMin = $_GET['goal_min'] ?? '';
    $goalMax = $_GET['goal_max'] ?? '';
    $dateStart = $_GET['date_start'] ?? '';
    $dateEnd = $_GET['date_end'] ?? '';
    $schoolName = $_GET['school_name'] ?? '';

    $query = "SELECT c.*, u.username AS school_name FROM campaigns c
              JOIN users u ON c.schoolAdmin_id = u.user_id
              WHERE c.status = 'Approved'";

    if (!empty($category)) {
        $query .= " AND c.category = '" . mysqli_real_escape_string($conn, $category) . "'";
    }

    if (!empty($search)) {
        $escapedSearch = mysqli_real_escape_string($conn, $search);
        $query .= " AND (c.campaign_name LIKE '%$escapedSearch%' OR c.description LIKE '%$escapedSearch%')";
    }

    if (is_numeric($goalMin)) {
        $query .= " AND c.target_amount >= " . (float)$goalMin;
    }

    if (is_numeric($goalMax)) {
        $query .= " AND c.target_amount <= " . (float)$goalMax;
    }

    if (!empty($dateStart)) {
        $query .= " AND c.start_date >= '" . mysqli_real_escape_string($conn, $dateStart) . "'";
    }

    if (!empty($dateEnd)) {
        $query .= " AND c.end_date <= '" . mysqli_real_escape_string($conn, $dateEnd) . "'";
    }

    if (!empty($schoolName)) {
        $query .= " AND u.username LIKE '%" . mysqli_real_escape_string($conn, $schoolName) . "%'";
    }

    $query .= " ORDER BY c.start_date DESC";

    $result = $conn->query($query);

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

// ✅ POST - Create new campaign with image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['campaignTitle'] ?? null;
    $description = $_POST['campaignDescription'] ?? null;
    $category = $_POST['campaignCategory'] ?? null;
    $goal = $_POST['targetAmount'] ?? null;
    $startDate = $_POST['startDate'] ?? null;
    $endDate = $_POST['endDate'] ?? null;
    $school_id = $_POST['school_id'] ?? null;
    $imagePath = null;

    // Validate required fields
    if (!$title || !$description || !$goal || !$startDate || !$endDate || !$school_id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Upload image if provided
    if (isset($_FILES['campaignImage']) && $_FILES['campaignImage']['error'] === 0) {
        $uploadsDir = "../uploads/";
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES["campaignImage"]["name"]);
        $targetPath = $uploadsDir . $filename;

        if (move_uploaded_file($_FILES["campaignImage"]["tmp_name"], $targetPath)) {
            $imagePath = "uploads/" . $filename; // relative path
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to upload image"]);
            exit;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO campaigns (campaign_name, description, category, target_amount, start_date, end_date, schoolAdmin_id, image_path, created_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
    $stmt->bind_param("sssissss", $title, $description, $category, $goal, $startDate, $endDate, $school_id, $imagePath);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Campaign created successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database insert failed: " . $stmt->error]);
    }

    exit;
}

// Fallback: Method not allowed
http_response_code(405);
echo json_encode(["error" => "Method Not Allowed"]);
exit;
