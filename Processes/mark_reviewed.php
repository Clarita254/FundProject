<?php
require_once("../includes/db_connect.php");
session_start();

if ($_SESSION['role'] !== 'systemAdmin') {
    exit("Unauthorized");
}

$id = intval($_GET['id'] ?? 0);
$conn->query("UPDATE suspicious_activity SET reviewed = TRUE WHERE id = $id");
header("Location: ../Dashboards/systemAdmindashboard.php");
?>
