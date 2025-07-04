<?php
header("Content-Type: application/json");

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Method not allowed."]);
    exit;
}

require_once("../includes/db_connect.php");

try {
    // Fetch top donors
    $sql = "
        SELECT u.username, SUM(d.amount) AS total_amount
        FROM donations d
        JOIN users u ON d.donor_id = u.user_id
        WHERE d.status = 'Completed' AND u.role = 'donor'
        GROUP BY d.donor_id
        ORDER BY total_amount DESC
        LIMIT 10
    ";

    $result = $conn->query($sql);

    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            "username" => $row['username'],
            "total_amount" => number_format((float)$row['total_amount'], 2)
        ];
    }

    echo json_encode($leaderboard, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Server error: " . $e->getMessage()]);
}
