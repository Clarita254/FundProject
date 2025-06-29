<?php
session_start();
require_once("../includes/db_connect.php");

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'guest';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Campaigns</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="../CSS/Campaign.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/navbar.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <?php include_once("../Templates/nav.php"); ?>

    <div class="main">
        <h1 class="page-title">CAMPAIGNS</h1>

        <!-- Campaigns List -->
        <div class="campaigns-container">
            <?php
            $query = "SELECT * FROM campaigns WHERE status = 'Approved' ORDER BY start_date DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
                    $title = htmlspecialchars($row['campaign_name']);
                    $description = htmlspecialchars($row['description']);
                    $target = (float)$row['target_amount'];
                    $raised = (float)$row['amount_raised'];
                    $endDate = date('d M Y', strtotime($row['end_date']));
                    $daysLeft = ceil((strtotime($row['end_date']) - time()) / 86400);
                    $image = (!empty($row['image_path']) && file_exists("../" . $row['image_path']))
                        ? "../" . $row['image_path']
                        : "https://via.placeholder.com/300x200";
                    $progress = $target > 0 ? min(100, ($raised / $target) * 100) : 0;
            ?>
            <div class="campaign-card">
                <div class="campaign-image" style="background-image: url('<?php echo $image; ?>')"></div>
                <div class="campaign-content">
                    <h3 class="campaign-title d-flex justify-content-between align-items-center">
  <?php echo $title; ?>
  <span class="badge 
    <?php
      switch (strtolower($row['status'])) {
        case 'approved': echo 'bg-success'; break;
        case 'pending': echo 'bg-warning text-dark'; break;
        case 'rejected': echo 'bg-danger'; break;
        default: echo 'bg-secondary';
      }
    ?>">
    <?php echo htmlspecialchars($row['status']); ?>
  </span>
</h3>

                    <p class="campaign-description"><?php echo $description; ?></p>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="raised-amount">Raised: sh<?php echo number_format($raised, 2); ?> of sh<?php echo number_format($target, 2); ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="days-left"><i class="far fa-clock me-1"></i><?php echo $daysLeft > 0 ? $daysLeft . ' days left' : 'Ended'; ?></span>
                        <a href="Donations.php?campaign_id=<?= $row['campaign_id']; ?>" class="btn btn-success donate-btn">Donate</a>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
                <p class="text-center mt-4">No campaign records .</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../Templates/Footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
