<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$breadcrumbs = $breadcrumbs ?? [];
?>

<div class="container-fluid pt-3 ps-3">
  <?php if (!empty($breadcrumbs)): ?>
    <a href="../Dashboards/donorDashboard.php" class="btn btn-outline-primary mb-2">
      <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item fw-bold text-primary">Dashboard</li>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
          <?php if ($index === array_key_last($breadcrumbs)): ?>
            <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page"><?= htmlspecialchars($crumb) ?></li>
          <?php else: ?>
            <li class="breadcrumb-item text-dark"><?= htmlspecialchars($crumb) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    </nav>
  <?php endif; ?>
</div>
