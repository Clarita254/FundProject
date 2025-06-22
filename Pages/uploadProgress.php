
<?php
session_start();
include_once("../includes/dbconnect.php"); // Your DB connection

// Check if school is logged in
if (!isset($_SESSION['school_id'])) {
    header("Location: signIn.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_id = $_SESSION['school_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $campaign_id = $_POST['campaign_id']; // optional, or fetch from context

    // File upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . uniqid() . "_" . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // Insert into database
            $stmt = $db->prepare("INSERT INTO progress_reports (school_id, campaign_id, title, description, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$school_id, $campaign_id, $title, $description, $imagePath]);

            $success = "Progress report uploaded successfully!";
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "Image upload failed. Error code: " . $_FILES['image']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Progress Report - EduFund</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="CSS/footer.css">
   <link rel="stylesheet" href="../CSS/Footer.css">
    <link rel="stylesheet" href="../CSS/navbar.css">
  
</head>
<body>

<?php include_once("Templates/nav.php"); ?>

<div class="container my-5">
  <h2 class="mb-4 text-primary">Upload Progress Report</h2>

  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form action="" method="POST" enctype="multipart/form-data" class="p-4 bg-white shadow rounded">
    <div class="mb-3">
      <label for="title" class="form-label">Report Title</label>
      <input type="text" name="title" id="title" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Progress Description</label>
      <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
      <label for="image" class="form-label">Upload Image</label>
      <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
    </div>

    <div class="mb-3">
      <label for="campaign_id" class="form-label">Campaign (optional)</label>
      <input type="number" name="campaign_id" id="campaign_id" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Submit Report</button>
  </form>
</div>

<?php include_once("Templates/Footer.php"); ?>

</body>
</html>
