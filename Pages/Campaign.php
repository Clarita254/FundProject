<?php
session_start();
require_once("../includes/db_connect.php");

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 'guest';

$category = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$query = "SELECT * FROM campaigns WHERE status = 'Approved'";

if (!empty($category)) {
    $escapedCategory = mysqli_real_escape_string($conn, $category);
    $query .= " AND category = '$escapedCategory'";
}

if (!empty($search)) {
    $escapedSearch = mysqli_real_escape_string($conn, $search);
    $query .= " AND (campaign_name LIKE '%$escapedSearch%' OR description LIKE '%$escapedSearch%')";
}

$query .= " ORDER BY start_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduFund - Campaigns</title>

  <!-- CSS & Bootstrap -->
  <link rel="stylesheet" href="../CSS/Campaign.css">
  <link rel="stylesheet" href="../CSS/footer.css">
  <link rel="stylesheet" href="../CSS/navbar.css">
  <link rel="stylesheet" href="../CSS/schoolAdminNav.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>


    
<?php include_once("../Templates/nav.php"); ?>


<!-- Category Sliding Banner -->
<div id="categoryBanner" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active" data-bs-interval="3000">
      <img src="../Images/sport1.jpg" class="d-block w-100" alt="Learners">
      <div class="carousel-caption">
        <h5>Fuel the game,Fund the Dream</h5>
      </div>
    </div>
    <div class="carousel-item" data-bs-interval="3000">
      <img src="../Images/learners2.jpg" class="d-block w-100" alt="Learning Resources">
      <div class="carousel-caption">
        <h5>Empowering Classrooms</h5>
      </div>
    </div>


    <div class="carousel-item" data-bs-interval="3000">
      <img src="../Images/digital device.jpg" class="d-block w-100" alt="Bridge technology gaps">
      <div class="carousel-caption">
        <h5>Bridging the Digital Divide, One Device at a Time.</h5>
      </div>
    </div>

    <div class="carousel-item" data-bs-interval="3000">
      <img src="../Images/Helpachildlearn.jpeg" class="d-block w-100" alt="Help children to learn">
      <div class="carousel-caption">
        <h5>Empower a mind today — educate a future tomorrow.</h5>
      </div>
    </div>

    <div class="carousel-item" data-bs-interval="3000">
      <img src="../Images/schoolinfrustructure.jpeg" class="d-block w-100" alt="School infrastructure">
      <div class="carousel-caption">
        <h5>Build a Better Tomorrow — One Classroom at a Time.</h5>
      </div>
    </div>

  </div>

  <!-- Optional controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#categoryBanner" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#categoryBanner" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>


<section class="explore-campaigns-section py-5">
  <div class="container">
    <h1 class="page-title text-center mb-4" style="color:rgb(6, 40, 75); font-weight: 700; font-family: 'Segoe UI', sans-serif;">
      <i class="fas fa-bullhorn me-2"></i> Explore Campaigns
    </h1>


  <!-- Filter Form -->
  <form method="GET" class="mb-4 px-3">
    <div class="row g-2 align-items-end">
      <div class="col-md-4 col-12">
        <label for="search" class="form-label">Search Campaigns</label>
        <input type="text" name="search" id="search" class="form-control" placeholder="e.g. library, desks" value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-4 col-8">
        <label for="category" class="form-label">Filter by Category</label>
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="">-- All Categories --</option>
          <option value="infrastructure" <?= $category == 'infrastructure' ? 'selected' : '' ?>>Infrastructure</option>
          <option value="learning" <?= $category == 'learning' ? 'selected' : '' ?>>Learning Resources</option>
          <option value="feeding" <?= $category == 'feeding' ? 'selected' : '' ?>>Feeding Program</option>
          <option value="digital" <?= $category == 'digital' ? 'selected' : '' ?>>Digital Learning</option>
          <option value="scholarship" <?= $category == 'scholarship' ? 'selected' : '' ?>>Scholarship</option>
          <option value="special_needs" <?= $category == 'special_needs' ? 'selected' : '' ?>>Special Needs</option>
          <option value="sanitation" <?= $category == 'sanitation' ? 'selected' : '' ?>>Sanitation</option>
          <option value="emergency" <?= $category == 'emergency' ? 'selected' : '' ?>>Emergency</option>
          <option value="sports" <?= $category == 'sports' ? 'selected' : '' ?>>Sports</option>
          <option value="uniforms" <?= $category == 'uniforms' ? 'selected' : '' ?>>Uniforms</option>
        </select>
      </div>
      <div class="col-md-4 col-4 d-grid">
        <button type="submit" class="btn" style="background-color: #2980b9; color: #fff; border-radius: 8px;">
          <i class="fas fa-filter me-1"></i> Apply Filters
        </button>
      </div>
    </div>
  </form>

  <!-- Active Filters Display -->
  <?php if (!empty($search) || !empty($category)): ?>
    <div class="mb-3 px-3">
      <span class="badge bg-secondary">
        Showing results for 
        <?= !empty($search) ? "search: <strong>$search</strong>" : '' ?>
        <?= (!empty($search) && !empty($category)) ? " & " : '' ?>
        <?= !empty($category) ? "category: <strong>" . ucwords(str_replace('_', ' ', $category)) . "</strong>" : '' ?>
      </span>
      <a href="campaign.php" class="btn btn-sm btn-outline-dark ms-2">Reset Filters</a>
    </div>
  <?php endif; ?>

  <!-- Campaign Cards -->
  <div class="campaigns-container">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
          $title = htmlspecialchars($row['campaign_name']);
          $description = htmlspecialchars($row['description']);
          $target = (float)$row['target_amount'];
          $raised = (float)$row['amount_raised'];
          $endDate = date('d M Y', strtotime($row['end_date']));
          $daysLeft = ceil((strtotime($row['end_date']) - time()) / 86400);
          $image = (!empty($row['image_path']) && file_exists("../" . $row['image_path']))
              ? "../" . $row['image_path']
              : "https://via.placeholder.com/300x200";
          $progress = $target > 0 ? min(100, ($raised / $target) * 100) : 0;
        ?>
        <div class="campaign-card">
          <div class="campaign-image lazy-bg" data-bg="<?= $image ?>"></div>
          <div class="campaign-content">
            <h3 class="campaign-title d-flex justify-content-between align-items-center">
              <?= $title ?>
              <span class="badge bg-success"><?= htmlspecialchars($row['status']) ?></span>
            </h3>
            <p class="campaign-description"><?= $description ?></p>
<div class="progress mb-2">
  <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
    <?= round($progress) ?>%
  </div>
</div>

            <p class="raised-amount">Raised: KES <?= number_format($raised, 2) ?> of KES <?= number_format($target, 2) ?></p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="days-left"><i class="far fa-clock me-1"></i><?= $daysLeft > 0 ? "$daysLeft days left" : 'Ended' ?></span>
              <a href="Donations.php?campaign_id=<?= $row['campaign_id'] ?>" class="btn btn-success donate-btn">Donate</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center mt-4 text-muted">No campaigns found for your criteria.</p>
    <?php endif; ?>
  </div>
</div>
</section>

<?php include_once("../Templates/Footer.php"); ?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Lazy Load Script -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const lazyBackgrounds = document.querySelectorAll('.lazy-bg');

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const bg = entry.target.getAttribute('data-bg');
          entry.target.style.backgroundImage = `url('${bg}')`;
          entry.target.classList.remove('lazy-bg');
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: "0px 0px 200px 0px"
    });

    lazyBackgrounds.forEach(el => observer.observe(el));
  });
</script>

</body>
</html>
