<?php
session_start();
require_once("../includes/db_connect.php");

// Only systemAdmin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'systemAdmin') {
    header("Location: ../Pages/signIn.php");
    exit();
}

require_once('../vendor/autoload.php');
use Dompdf\Dompdf;

// Load EduFund logo and convert to base64
$logoPath = '../assets/Edufund.png';
$logoBase64 = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,' . $logoBase64;

// Fetch suspicious activity data
$query = "SELECT sa.id, u.username, sa.description, sa.timestamp, sa.reviewed
          FROM suspicious_activity sa
          LEFT JOIN users u ON sa.user_id = u.user_id
          ORDER BY sa.timestamp DESC";
$result = $conn->query($query);

// Start building HTML
$html = '
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
  h2 { text-align: center; margin-bottom: 10px; }
  img.logo { display: block; margin: 0 auto 10px auto; width: 120px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #444; padding: 8px; text-align: left; }
  th { background-color: #f2f2f2; }
</style>

<img class="logo" src="' . $logoSrc . '" alt="EduFund Logo" />
<h2>Suspicious Activity Report</h2>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Description</th>
      <th>Time</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . htmlspecialchars($row['username'] ?? 'Unknown') . '</td>
                    <td>' . htmlspecialchars(substr($row['description'], 0, 100)) . '</td>
                    <td>' . date('d M Y H:i', strtotime($row['timestamp'])) . '</td>
                    <td>' . ($row['reviewed'] ? 'Reviewed' : 'Unreviewed') . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="5">No suspicious activity found.</td></tr>';
}

$html .= '</tbody></table>';

// Initialize Dompdf and generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Force download
$dompdf->stream("suspicious_activity_report.pdf", ["Attachment" => true]);
exit;
?>
