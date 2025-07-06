<?php
session_start();
require_once("../includes/db_connect.php");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - About Us</title>

<link rel="stylesheet" href="../CSS/AboutUs.css">
<link rel="stylesheet" href="../CSS/footer.css">
<link rel="stylesheet" href="../CSS/navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
       <!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>
  
    
    <!-- Main Content -->
    <div class="main">
        <h1 class="page-title">ABOUT US</h1>
        
        <div class="content-section">
            <div class="about-text">
                <p>EduFund is a crowdfunding platform committed to revolutionizing education by linking generous donors with schools and educational initiatives in need. </p>
                <p>We offer a transparent and trustworthy way for individuals and organizations to contribute directly to education, guaranteeing that every donation is used exactly as intended.</p>
                <p>By connecting donors with educational institutions, we are re-opening doors for students who may not have had access to the resources they need for a quality education.</p>
            </div>
            
        </div>
        
        <div class="content-section">
            <h2 class="section-title">OUR MISSION</h2>
            <div class="section-text">
                <p>To democratize access to educational funding by creating a transparent, efficient platform that connects donors directly with verified schools.</p>
                <p>We believe every student deserves equal opportunities to learn and grow, regardless of their socioeconomic background.</p>
            </div>
        </div>
        <div class="content-section">
            <h2 class="section-title">OUR VISION</h2>
            <div class="section-text">
                <p>To become the leading educational crowdfunding platform in Africa, ensuring that no student is left behind due to lack of funding or resources. We envision a future where every child has access to quality education, supported by a global community that believes in their potential.</p>
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
    
    <?php include_once("../Templates/Footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>