<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Leaderboard</title>
    <link rel="stylesheet" href="css/Leaderboard.css">
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="logo">EduFund</div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about-us.php">About us</a></li>
                <li><a href="campaigns.php">Campaign</a></li>
                <li><a href="donation-history.php">Donation History</a></li>
                <li><a href="logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <!-- Main Content -->
    <div class="main">
        <h1 class="page-title">LEADERBOARD</h1>
        
        <!-- Leaderboard Table -->
        <div class="leaderboard-container">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>RANK</th>
                        <th>USER</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody id="leaderboard-data">
                    <!-- Data will be populated by JavaScript from API -->
                    <tr>
                        <td colspan="3" class="loading-message">Loading leaderboard data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> EduFund. All rights reserved.</p>
        <p>Contact us: info@edufund.org</p>
    </footer>

    <!-- JavaScript for API Fetch -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchLeaderboardData();
        });

        function fetchLeaderboardData() {
            fetch('api/leaderboard.php')
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
                        userCell.textContent = user.username.length > 8 
                            ? `${user.username.substring(0, 8)}...` 
                            : user.username;
                        
                        const amountCell = document.createElement('td');
                        amountCell.textContent = `$${user.total_amount}`;
                        
                        row.appendChild(rankCell);
                        row.appendChild(userCell);
                        row.appendChild(amountCell);
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error fetching leaderboard data:', error);
                    document.getElementById('leaderboard-data').innerHTML = 
                        '<tr><td colspan="3">Error loading leaderboard data</td></tr>';
                });
        }
    </script>
</body>
</html>