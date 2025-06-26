<?php
require_once("../includes/db_connect.php");
session_start();

// Optional: Only allow admins
// if ($_SESSION['role'] !== 'admin') {
//     header("Location: ../login.php");
//     exit;
// }

// Fetch analytics
$totalFunds = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(target_amount) AS total FROM campaigns"))['total'] ?? 0;
$totalCampaigns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM campaigns"))['count'] ?? 0;
$pendingApprovals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM schools WHERE status='pending'"))['count'] ?? 0;
$flaggedTxns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM transactions WHERE flagged=1"))['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduFund</title>
    <link rel="stylesheet" href="../CSS/Admin.css">
    <link rel="stylesheet" href="../CSS/Footer.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include_once("../Templates/nav.php"); ?>

<div class="container-fluid mt-4">
    <h2 class="mb-4">System Administrator Dashboard</h2>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5>Total Funds Raised</h5>
                    <p class="fs-4">$<?= number_format($totalFunds) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <h5>Total Campaigns</h5>
                    <p class="fs-4"><?= $totalCampaigns ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <h5>Pending Approvals</h5>
                    <p class="fs-4"><?= $pendingApprovals ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <h5>Flagged Transactions</h5>
                    <p class="fs-4"><?= $flaggedTxns ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><i class="fas fa-user-check me-2"></i>Pending School & Campaign Approvals</div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Pending schools
                    $schools = mysqli_query($conn, "SELECT * FROM schools WHERE status='pending'");
                    while ($school = mysqli_fetch_assoc($schools)) {
                        echo "<tr>
                            <td>School</td>
                            <td>{$school['name']}</td>
                            <td>{$school['contact_email']}</td>
                            <td>{$school['submitted_at']}</td>
                            <td>
                                <button class='btn btn-sm btn-success'>Approve</button>
                                <button class='btn btn-sm btn-danger'>Reject</button>
                            </td>
                        </tr>";
                    }

                    // Pending campaigns
                    $campaigns = mysqli_query($conn, "SELECT * FROM campaigns WHERE status='pending'");
                    while ($campaign = mysqli_fetch_assoc($campaigns)) {
                        echo "<tr>
                            <td>Campaign</td>
                            <td>{$campaign['title']}</td>
                            <td>{$campaign['creator_email']}</td>
                            <td>{$campaign['created_at']}</td>
                            <td>
                                <button class='btn btn-sm btn-success'>Approve</button>
                                <button class='btn btn-sm btn-danger'>Reject</button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Real-Time Transactions -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white"><i class="fas fa-exchange-alt me-2"></i>Live Transactions</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Flagged</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $txns = mysqli_query($conn, "SELECT * FROM transactions ORDER BY created_at DESC LIMIT 10");
                    while ($txn = mysqli_fetch_assoc($txns)) {
                        $flagged = $txn['flagged'] ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-success">No</span>';
                        echo "<tr>
                            <td>{$txn['id']}</td>
                            <td>{$txn['user_email']}</td>
                            <td>$" . number_format($txn['amount']) . "</td>
                            <td>{$txn['status']}</td>
                            <td>$flagged</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Role Management -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white"><i class="fas fa-user-cog me-2"></i>Manage Users</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = mysqli_query($conn, "SELECT * FROM users");
                    while ($user = mysqli_fetch_assoc($users)) {
                        $suspendBtn = $user['status'] === 'active'
                            ? "<button class='btn btn-sm btn-danger'>Suspend</button>"
                            : "<button class='btn btn-sm btn-secondary' disabled>Suspended</button>";
                        echo "<tr>
                            <td>{$user['name']}</td>
                            <td>{$user['email']}</td>
                            <td>{$user['role']}</td>
                            <td>{$user['status']}</td>
                            <td>$suspendBtn</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../Templates/Footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
