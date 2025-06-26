<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3 sticky-top" style="background-color: #001f3f;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" style="color: #7fdbff;" href="../Pages/Home.php">EduFund</a>

    <!-- Mobile toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Donor links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Home.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/Home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'AboutUs.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/AboutUs.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Campaign.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/Campaign.php">Campaigns</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Donationhistory.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/Donationhistory.php">Donation History</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'Leaderboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/Leaderboard.php">Leaderboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'donorDashboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Dashboards/donorDashboard.php">Dashboard</a>
        </li>
      </ul>

      <!-- Logout -->
      <div class="d-flex">
        <a href="../includes/logout.php" class="btn btn-outline-info me-2">Logout</a>
      </div>
    </div>
  </div>
</nav>
