<?php
session_start();
require_once("../includes/db_connect.php");

// Ensure only admin can access
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../signIn.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['doc_id'], $_POST['action'])) {
    $doc_id = $_POST['doc_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    $status = $action === 'approve' ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE documents SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $doc_id);
    $stmt->execute();
}

// Fetch documents
$result = $conn->query("SELECT d.id, d.file_path, d.status, s.name AS school_name
                        FROM documents d
                        JOIN schools s ON d.school_id = s.id
                        WHERE d.status = 'pending'");
?>

<h2>Pending Document Verifications</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div>
        <p><strong>School:</strong> <?= htmlspecialchars($row['school_name']) ?></p>
        <p><a href="<?= $row['file_path'] ?>" target="_blank">View Document</a></p>
        <form method="POST">
            <input type="hidden" name="doc_id" value="<?= $row['id'] ?>">
            <button type="submit" name="action" value="approve">Approve</button>
            <button type="submit" name="action" value="reject">Reject</button>
        </form>
    </div>
    <hr>
<?php endwhile; ?>
