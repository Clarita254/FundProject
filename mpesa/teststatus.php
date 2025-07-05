<?php
require_once('../includes/db_connect.php');
require_once('stk_status_query.php'); 

$donation_id = 46; // Set this manually
queryAndUpdateDonationStatus($conn, $donation_id);

echo "Done checking donation #$donation_id";
?>
