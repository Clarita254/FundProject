<?php
// Get current page filename for active link detection
$currentPage=(basename($_SERVER['PHP_SELF']));
?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3 sticky-top" style="background-color: #001f3f;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" style="color: #7fdbff;" href="Home.php">EduFund</a>


    <!----Toggle button for mobile view---->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left Nav Links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Home.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="Home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'AboutUs.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="AboutUs.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Campaign.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="Campaign.php">Campaigns</a>
        </li>
         <li class="nav-item">
       <a class="nav-link <?= ($currentPage == 'CampaignCreation.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="CampaignCreation.php">Create Campaign</a>
       </li>

        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Leaderboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="Leaderboard.php">Leaderboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Donationhistory.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="Donationhistory.php">Donation History</a>

          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'donor'): ?>
  <li class="nav-item">
    <a class="nav-link <?= ($currentPage == 'donorDashboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="donorDashboard.php">Dashboard</a>
  </li>

<?php endif; ?>

        </li>

         <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'ProgressForm.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="ProgressForm.php">Progress Form</a>
        </li>
      </ul>

      <!-- Search Bar -->
      <form class="d-flex me-3" action="search.php" method="GET">
        <input class="form-control me-2" type="search" name="query" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-info" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>

      <!-- Auth Buttons -->
      <div class="d-flex">
        <a href="signup.php" class="btn btn-outline-info me-2 <?= ($currentPage == 'signUp.php') ? 'fw-bold' : '' ?>">Sign Up</a>
        <a href="signin.php" class="btn btn-info text-dark <?= ($currentPage == 'signIn.php') ? 'fw-bold' : '' ?>">Sign In</a>
      </div>

  

      <?php if (isset($_SESSION['user_id'])): ?>
    <a href="../includes logout.php" class="btn btn-outline-info me-2">Logout</a>

    
    <?php endif; ?>

      
    </div>
  </div>
</nav>

