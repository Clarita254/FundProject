<?php
header("Content-Type: application/json");
session_start();
require_once("../includes/db_connect.php");

// Ensure only donors access this endpoint
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized. Please log in as a donor."]);
    exit;
}

$donorId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        d.donation_date,
        c.campaign_name,
        d.amount,
        d.payment_mode,
        d.status
    FROM donations d
    JOIN campaigns c ON d.campaign_id = c.campaign_id
    WHERE d.donor_id = ?
    ORDER BY d.donation_date DESC
");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$result = $stmt->get_result();

$history = [];

while ($row = $result->fetch_assoc()) {
    $history[] = [
        "date" => $row["donation_date"],
        "campaign" => $row["campaign_name"],
        "amount" => $row["amount"],
        "payment_mode" => $row["payment_mode"],
        "status" => $row["status"]
    ];
}

echo json_encode([
    "success" => true,
    "donations" => $history
]);
?>