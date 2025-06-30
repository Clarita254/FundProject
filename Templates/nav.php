<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'guest';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-3 sticky-top" style="background-color: #001f3f;">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" style="color: #7fdbff;" href="../Pages/Home.php">EduFund</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Common Links -->
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
          <a class="nav-link <?= ($currentPage == 'Leaderboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Pages/Leaderboard.php">Leaderboard</a>
        </li>

        <!-- School Admin Links -->
        <?php if ($role === 'schoolAdmin'): ?>
          <li class="nav-item">
            <a class="nav-link <?= ($currentPage == 'schoolAdmindashboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Dashboards/schoolAdmindashboard.php">School Dashboard</a>
          </li>

        <!-- Donor Links -->
        <?php elseif ($role === 'donor'): ?>
          
          <li class="nav-item">
            <a class="nav-link <?= ($currentPage == 'donorDashboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Dashboards/donorDashboard.php">Donor Dashboard</a>
          </li>

        <!-- System Admin Links -->
        <?php elseif ($role === 'systemAdmin'): ?>
          <li class="nav-item">
            <a class="nav-link <?= ($currentPage == 'systemAdminDashboard.php') ? 'active fw-bold text-info' : 'text-white' ?>" href="../Dashboards/systemAdminDashboard.php">Admin Dashboard</a>
          </li>
          
        <?php endif; ?>
      </ul>

      <!-- Auth Buttons -->
      <div class="d-flex">
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="../Pages/signUp.php" class="btn btn-outline-info me-2">Sign Up</a>
          <a href="../Pages/signIn.php" class="btn btn-info text-dark">Sign In</a>
        <?php else: ?>
          <a href="../includes/logout.php" class="btn btn-outline-info">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
