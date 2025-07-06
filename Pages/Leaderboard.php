<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFund - Leaderboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/Leaderboard.css">
    <link rel="stylesheet" href="../CSS/Footer.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
</head>
<body>

<?php include_once("../Templates/nav.php"); ?>

<!-- Main Content -->
<div class="container my-5">
    <h1 class="text-center mb-4 text-primary">🏆 Donor Leaderboard</h1>

    <!-- Leaderboard Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>RANK</th>
                    <th>DONOR NAME</th>
                    <th>TOTAL DONATED (KES)</th>
                </tr>
            </thead>
            <tbody id="leaderboard-data">
                <!-- Data will be populated by JavaScript from API -->
                <tr>
                    <td colspan="3" class="text-muted">Loading leaderboard data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include_once("../Templates/Footer.php"); ?>

<!-- JavaScript to Fetch Leaderboard Data -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('../api/leaderboard.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('leaderboard-data');
            tbody.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-muted">No leaderboard data available</td></tr>';
                return;
            }

            data.forEach((user, index) => {
                const row = document.createElement('tr');

                const rankCell = document.createElement('td');
                rankCell.textContent = index + 1;

                const userCell = document.createElement('td');
                userCell.textContent = user.username.length > 15 
                    ? user.username.substring(0, 15) + '...' 
                    : user.username;

                const amountCell = document.createElement('td');
                amountCell.textContent = `KES ${user.total_amount}`;

                row.appendChild(rankCell);
                row.appendChild(userCell);
                row.appendChild(amountCell);

                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error("Error fetching leaderboard:", error);
            document.getElementById('leaderboard-data').innerHTML = 
                '<tr><td colspan="3" class="text-danger">Failed to load data.</td></tr>';
        });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
