<?php
session_start();
require_once("../includes/db_connect.php");

// Fetch top 3 approved campaigns (can modify based on criteria)
$featuredQuery = "SELECT campaign_id, campaign_name, description, image_path FROM campaigns WHERE status = 'Approved' ORDER BY start_date DESC LIMIT 3";
$featuredResult = mysqli_query($conn, $featuredQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Home</title>

    <!---CSS----->
    <link rel="stylesheet" href="../CSS/Home.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
   <!---Include Header---->
   <?php include_once("../Templates/nav.php"); ?>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Fund Education, Change Lives</h1>
        <p>Join our platform to support students in need.</p>
        <a href="../Pages/signUp.php" class="btn btn-primary">Start a Campaign</a>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <h2>How It Works</h2>
        <div class="info-cards">
            <div class="info-card">
                <h3>Create a Campaign</h3>
                <p>Schools can set up education funding campaigns in minutes. Share their story and set funding goals.</p>
            </div>
            <div class="info-card">
                <h3>Spread the Word</h3>
                <p>Share the campaign with friends, family and social networks to gather support.</p>
            </div>
            <div class="info-card">
                <h3>Receive Funding</h3>
                <p>Collect donations and watch the campaign grow. Funds go directly to educational needs.</p>
            </div>
        </div>
    </section>

    <!-- Why EduFund Section -->
    <section class="why-edufund">
        <div class="why-content">
            <h2>Why Choose EduFund?</h2>
            <p>EduFund is your trusted bridge between generous donors and schools in need. We provide a secure, transparent, and easy-to-use platform that connects under-resourced schools with individuals and organizations ready to make a real difference. Every campaign is carefully verified, ensuring your support goes exactly where it’s needed — empowering students, improving learning environments, and transforming futures through education.
</p>
        </div>
    </section>

    <!-- Dynamic Featured Campaigns -->
    <section class="campaigns">
        <h2>Featured Campaigns</h2>
        <div class="campaign-grid">
            <?php if ($featuredResult && mysqli_num_rows($featuredResult) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($featuredResult)): 
                   $imagePath = "../" . $row['image_path'];
$image = (!empty($row['image_path']) && file_exists($imagePath)) ? $imagePath : "https://via.placeholder.com/300x200";

                ?>
                    <div class="campaign-card">
                        <div class="campaign-img">
                            <img src="<?= $image ?>" alt="<?= htmlspecialchars($row['campaign_name']) ?>" loading="lazy" />
                        </div>
                        <div class="campaign-info">
                            <h3><?= htmlspecialchars($row['campaign_name']) ?></h3>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <a href="../Pages/Campaign.php" class="learn-more-btn">Learn More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-muted">No featured campaigns available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!------Include footer-->
    <?php include_once("../Templates/Footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
