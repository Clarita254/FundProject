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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
      .banner {
          width: 100%;
          max-height: 400px;
          overflow: hidden;
      }

      .banner img {
          width: 100%;
          height: auto;
          object-fit: cover;
          display: block;
      }
    </style>
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<!-- ✅ Banner with Lazy Loading -->
<div class="banner">
    <img src="../assets/banner.jpg" alt="About EduFund Banner" loading="lazy">
</div>

<!-- Main Content -->
<div class="main">
    <h1 class="page-title">ABOUT US</h1>

    <div class="content-section">
        <div class="about-text">
            <p>EduFund is a transparent and purpose-driven crowdfunding platform dedicated to transforming education across Kenya and beyond. Our mission is to bridge the gap between under-resourced schools and individuals, institutions, and organizations ready to support meaningful educational change.</p>
            <p>We provide a secure and user-friendly space where schools can share their verified needs and donors can contribute directly to impactful causes. By championing openness, accountability, and trust, EduFund ensures that every donation creates real, visible outcomes in the lives of students and educators.</p>
        </div>
    </div>

    <div class="content-section">
        <h2 class="section-title">OUR MISSION</h2>
        <div class="section-text">
            <p>To make quality education accessible to every child by connecting donors with verified schools and education-based projects. We aim to foster equity in education through transparent funding, empowering communities and creating long-term academic success for all learners — regardless of their background.</p>
        </div>
    </div>

    <div class="content-section">
        <h2 class="section-title">OUR VISION</h2>
        <div class="section-text">
            <p>To be the leading educational crowdfunding platform in Africa — a trusted hub where every learner, regardless of geography or circumstance, has the opportunity to thrive through the power of collective giving. We envision a future where education is not a privilege, but a universal right supported by a global community.</p>
        </div>
    </div>

    <div class="content-section">
        <h2 class="section-title">OUR VALUES</h2>
        <ul class="values-list">
            <li><strong>Transparency:</strong> We are committed to honest communication and financial accountability. Every shilling is traceable, and every story is real.</li>
            <li><strong>Impact:</strong> We focus on measurable, meaningful outcomes — from improved classrooms to empowered students and stronger communities.</li>
            <li><strong>Inclusivity:</strong> Our platform supports all learners, regardless of location, background, or ability, ensuring equal opportunities for all.</li>
            <li><strong>Collaboration:</strong> We believe in shared responsibility. By connecting schools, donors, and communities, we build collective power for lasting change.</li>
            <li><strong>Innovation:</strong> We harness technology to streamline giving, improve access, and ensure that educational support is only a few clicks away.</li>
        </ul>
    </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

