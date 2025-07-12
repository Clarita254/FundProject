<?php
require_once("../includes/db_connect.php");
$user_id = 2; // Replace with a real user ID
$desc = "Test suspicious donation modification attempt";
$now = date("Y-m-d H:i:s");

$sql = "INSERT INTO suspicious_activity (user_id, description, timestamp, reviewed)
        VALUES ($user_id, '$desc', '$now', 0)";

if ($conn->query($sql)) {
    echo "Suspicious activity logged successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
