<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Campaigns</title>
    <link rel="stylesheet" href="css/Campaign.css">
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo">EduFund</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about-us.php">About us</a></li>
                <li><a href="leaderboard.php">Leaderboard</a></li>
                <li><a href="donation-history.php">Donation History</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Content -->
    <div class="main">
        <h1 class="page-title">CAMPAIGN</h1>
        
        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search for campaigns...">
        </div>
        
        <!-- Campaigns List -->
        <div class="campaigns-container">
            <div class="campaign-card">
                <h3 class="campaign-title">School Renovation Project</h3>
                <p class="raised-amount">Raised: $8,250 of $15,000</p>
                <button class="donate-btn">Donate</button>
            </div>
            
            <div class="campaign-card">
                <h3 class="campaign-title">STEM Equipment Fund</h3>
                <p class="raised-amount">Raised: $12,300 of $20,000</p>
                <button class="donate-btn">Donate</button>
            </div>
            
            <div class="campaign-card">
                <h3 class="campaign-title">Library Books Initiative</h3>
                <p class="raised-amount">Raised: $3,450 of $10,000</p>
                <button class="donate-btn">Donate</button>
            </div>
            
            <div class="campaign-card">
                <h3 class="campaign-title">School Meal Program</h3>
                <p class="raised-amount">Raised: $5,670 of $8,000</p>
                <button class="donate-btn">Donate</button>
            </div>
            
            <div class="campaign-card">
                <h3 class="campaign-title">Sports Equipment Drive</h3>
                <p class="raised-amount">Raised: $2,100 of $5,000</p>
                <button class="donate-btn">Donate</button>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> EduFund. All rights reserved.</p>
        <p>Contact us: info@edufund.org</p>
    </footer>
</body>
</html>