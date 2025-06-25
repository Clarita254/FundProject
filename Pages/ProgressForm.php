<?php
// progressReport.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "your_username", "your_password", "your_database");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $schoolName = htmlspecialchars($_POST['school_name']);
    $title = htmlspecialchars($_POST['report_title']);
    $description = htmlspecialchars($_POST['report_description']);
    $amountUsed = floatval($_POST['amount_used']);
    $date = $_POST['report_date'];

    $uploadDir = "uploads/";
    $uploadedPhotos = [];
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['progress_photos']['tmp_name'] as $key => $tmp_name) {
        $fileName = basename($_FILES['progress_photos']['name'][$key]);
        $targetFilePath = $uploadDir . time() . "_" . $fileName;
        if (move_uploaded_file($tmp_name, $targetFilePath)) {
            $uploadedPhotos[] = $targetFilePath;
        }
    }

    $photoPaths = implode(",", $uploadedPhotos);

    $stmt = $conn->prepare("INSERT INTO progress_reports (school_name, title, description, amount_used, report_date, photos) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $schoolName, $title, $description, $amountUsed, $date, $photoPaths);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Progress report submitted successfully!'); window.location.href='progressReport.php';</script>";
    } else {
        echo "<script>alert('Failed to submit report.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Progress Report</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="../CSS/progressReport.css">
  <link rel="stylesheet" href="../CSS/Footer.css">
   <link rel="stylesheet" href="../CSS/navbar.css">

</head>
<body class="progress-report-body">
 <!---Include Header---->
  <?php include_once("../Templates/nav.php"); ?>

<div class="container">
  <div class="progress-form-card">
    <h2 class="form-title mb-4">Submit Fund Utilization Report</h2>
    <form method="POST" enctype="multipart/form-data">

      <div class="mb-3">
        <label class="form-label">School Name</label>
        <input type="text" name="school_name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Report Title</label>
        <input type="text" name="report_title" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Fund utilization Description</label>
        <textarea name="report_description" class="form-control" rows="4" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Amount Used (Ksh)</label>
        <input type="number" name="amount_used" step="0.01" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Report Date</label>
        <input type="date" name="report_date" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Supporting Progress Images</label>
        <input type="file" name="progress_photos[]" class="form-control" accept="image/*" multiple>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Submit Report</button>
      </div>

    </form>
  </div>
</div>

 <?php include_once("../Templates/Footer.php"); ?>
</body>
</html>
