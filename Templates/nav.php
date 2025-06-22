<?php
// Get current page filename for active link detection
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm px-3 sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-success" href="index.php">EduFund</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left Nav Links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'index.php') ? 'active fw-bold text-success' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'about.php') ? 'active fw-bold text-success' : '' ?>" href="about.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'campaigns.php') ? 'active fw-bold text-success' : '' ?>" href="campaigns.php">Campaigns</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'leaderboard.php') ? 'active fw-bold text-success' : '' ?>" href="leaderboard.php">Leaderboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'donationHistory.php') ? 'active fw-bold text-success' : '' ?>" href="donationHistory.php">Donation History</a>
        </li>
      </ul>

      <!-- Search Bar -->
      <form class="d-flex me-3" action="search.php" method="GET">
        <input class="form-control me-2" type="search" name="query" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
          <i class="fas fa-search"></i>
        </button>
      </form>

      <!-- Auth Buttons -->
      <div class="d-flex">
        <a href="signUp.php" class="btn btn-outline-success me-2 <?= ($currentPage == 'signUp.php') ? 'fw-bold' : '' ?>">Sign Up</a>
        <a href="signIn.php" class="btn btn-success <?= ($currentPage == 'signIn.php') ? 'fw-bold' : '' ?>">Sign In</a>
      </div>
    </div>
  </div>
</nav>