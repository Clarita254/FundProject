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
</head>
<body>
   <!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>

  
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
    
  <section class="campaigns">
    <h2>Featured Campaigns</h2>
    <div class="campaign-grid">
        <div class="campaign-card">
            <div class="campaign-img">
                <img src="Images/Helpachildlearn.png" alt="Help a Child Learn" />
            </div>
            <div class="campaign-info">
                <h3>Help a Child Learn</h3>
                <p>Support a child in need of educational resources.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
        </div>
        <div class="campaign-card">
            <div class="campaign-img">
                <img src="Images/schoolsupplies.png" alt="School Supplies Drive" />
            </div>
            <div class="campaign-info">
                <h3>School Supplies Drive</h3>
                <p>Providing materials for underprivileged students.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
        </div>
        <div class="campaign-card">
            <div class="campaign-img">
                <img src="Images/schoolinfrastructure.png" alt="Public School Renovation" />
            </div>
            <div class="campaign-info">
                <h3>Public School Renovation</h3>
                <p>Help improve facilities and learning environments in public schools.</p>
                <a href="#" class="learn-more-btn">Learn More</a>
            </div>
        </div>
    </div>
</section>

    
    <!------Include footer-->
   <?php include_once("../Templates/Footer.php"); ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>