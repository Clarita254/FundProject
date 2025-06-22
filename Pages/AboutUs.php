<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - About Us</title>
  <link rel="stylesheet" href="../CSS/AboutUs.css">

</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo">EduFund</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="campaigns.php">Campaign</a></li>
                <li><a href="leaderboard.php">Leaderboard</a></li>
                <li><a href="donation-history.php">Donation History</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Content -->
    <div class="main">
        <h1 class="page-title">ABOUT US</h1>
        
        <div class="content-section">
            <div class="about-text">
                <p>EduFund is a crowdfunding platform dedicated to transforming education by connecting donors with schools and educational projects in need. Our hope and prayer is to impact the lives of thousands of students.</p>
                <p>Our platform provides a transparent way for individuals and organizations to support education directly, ensuring that every shilling donated goes exactly where it's needed most.</p>
                <p>By bridging the gap between donors and educational institutions, we're creating opportunities for students who might otherwise lack access to quality education resources.</p>
            </div>
            
            <div class="divider"></div>
            <div class="divider"></div>
            <div class="divider"></div>
        </div>
        
        <div class="content-section">
            <h2 class="section-title">OUR MISSION</h2>
            <div class="section-text">
                <p>To democratize access to educational funding by creating a transparent, efficient platform that connects donors directly with verified schools.</p>
                <p>We believe every student deserves equal opportunities to learn and grow, regardless of their socioeconomic background.</p>
            </div>
        </div>
        
        <div class="content-section">
            <h2 class="section-title">OUR VALUES</h2>
            <ul class="values-list">
                <li><strong>Transparency:</strong> Every donation is tracked and reported.</li>
                <li><strong>Impact:</strong> We measure success by school reports.</li>
                <li><strong>Community:</strong> Education is a shared responsibility.</li>
                <li><strong>Inclusivity:</strong> Reaches underserved communities without bias.</li>
            </ul>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> EduFund. All rights reserved.</p>
        <p>Contact us: info@edufund.org</p>
    </footer>
</body>
</html>