<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Campaigns</title>
    
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



<link rel="stylesheet" href="../CSS/Campaign.css">
<link rel="stylesheet" href="../CSS/navbar.css">
<link rel="stylesheet" href="../CSS/footer.css">
</head>
<!-- Bootstrap CSS -->


<body>
<!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>
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

   <?php include_once("../Templates/Footer.php");?>
</body>
</html>