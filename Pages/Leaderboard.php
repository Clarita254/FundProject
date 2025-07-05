<?php
session_start();
require_once("../includes/db_connect.php");

// Only allow logged-in users (optional restriction)
$role = $_SESSION['role'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EduFund - Leaderboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../CSS/Leaderboard.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
</head>
<body>

  <!-- Navigation -->
  <?php include_once("../Templates/nav.php"); ?>

  <!-- Main Content -->
  <div class="container py-5">
    <h1 class="page-title text-center text-primary mb-4" style="font-family: 'Segoe UI', sans-serif;">
      <i class="fas fa-trophy me-2"></i> Donor Leaderboard
    </h1>

    <div class="leaderboard-container">
      <table class="leaderboard-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>User</th>
            <th>Total Donated (KES)</th>
          </tr>
        </thead>
        <tbody id="leaderboard-data">
          <tr>
            <td colspan="3" class="loading-message">Loading leaderboard data...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Footer -->
  <?php include_once("../Templates/Footer.php"); ?>

  <!-- JavaScript Fetch for API -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      fetchLeaderboardData();
    });

    function fetchLeaderboardData() {
      fetch('../api/leaderboard.php')
        .then(response => response.json())
        .then(data => {
          const tbody = document.getElementById('leaderboard-data');
          tbody.innerHTML = '';

          if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">No leaderboard data available</td></tr>';
            return;
          }

          data.forEach((user, index) => {
            const row = document.createElement('tr');

            const rankCell = document.createElement('td');
            rankCell.textContent = index + 1;

            const userCell = document.createElement('td');
            userCell.textContent = user.username.length > 12 
              ? user.username.substring(0, 12) + '...' 
              : user.username;

            const amountCell = document.createElement('td');
            amountCell.textContent = `KES ${parseFloat(user.total_amount).toLocaleString()}`;

            row.appendChild(rankCell);
            row.appendChild(userCell);
            row.appendChild(amountCell);
            tbody.appendChild(row);
          });
        })
        .catch(error => {
          console.error('Error fetching leaderboard:', error);
          document.getElementById('leaderboard-data').innerHTML = 
            '<tr><td colspan="3">Error loading leaderboard</td></tr>';
        });
    }
  </script>
</body>
</html>
