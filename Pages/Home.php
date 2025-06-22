<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Home</title>
    <link rel="stylesheet" href="../CSS/Home.css">
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo">EduFund</div>
        <nav>
            <ul>
                <li><a href="about-us.php">About Us</a></li>
                <li><a href="campaigns.php">Campaign</a></li>
                <li><a href="leaderboard.php">Leaderboard</a></li>
                <li><a href="donation-history.php">Donation History</a></li>
                <li><a href="login.php" class="auth-link">Sign In/Sign Up</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <h1>Fund Education, Change Lives</h1>
        <p>Join our platform to support students in need.</p>
        <button>Start a Campaign</button>
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
            <p>EduFund bridges the gap between under-resourced schools and potential donors by providing a transparent, centralized platform for funding verified educational needs.</p>
        </div>
    </section>
    
    <!-- Campaigns Section -->
    <section class="campaigns">
        <h2>Featured Campaigns</h2>
        <div class="campaign-grid">
            <div class="campaign-card">
                <div class="campaign-img" style="background-color: #a3b8cc;"></div>
                <div class="campaign-info">
                    <h3>Teachers Salary Support</h3>
                    <p>Support teachers' salaries in underserved public schools.</p>
                    <a href="#" class="learn-more-btn">Learn More</a>
                </div>
            </div>
            <div class="campaign-card">
                <div class="campaign-img" style="background-color: #a3b8cc;"></div>
                <div class="campaign-info">
                    <h3>School Supplies Drive</h3>
                    <p>Providing materials for underprivileged students.</p>
                    <a href="#" class="learn-more-btn">Learn More</a>
                </div>
            </div>
            <div class="campaign-card">
                <div class="campaign-img" style="background-color: #a3b8cc;"></div>
                <div class="campaign-info">
                    <h3>Public School Renovation</h3>
                    <p>Help improve facilities and learning environments in public schools.</p>
                    <a href="#" class="learn-more-btn">Learn More</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> EduFund. All rights reserved.</p>
        <p>Contact us: info@edufund.org</p>
    </footer>
</body>
</html>