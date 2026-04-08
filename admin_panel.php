<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

enforceCorrectDashboard('admin');
$me = getCurrentUser();
$flash = getFlash();

$stats = ['users'=>0,'facilities'=>0,'items'=>0,'facility_bookings'=>0,'item_bookings'=>0];
try {
    $stats['users'] = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $stats['facilities'] = (int)$pdo->query('SELECT COUNT(*) FROM facilities')->fetchColumn();
    $stats['items'] = (int)$pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
    $stats['facility_bookings'] = (int)$pdo->query('SELECT COUNT(*) FROM facility_bookings')->fetchColumn();
    $stats['item_bookings'] = (int)$pdo->query('SELECT COUNT(*) FROM item_bookings')->fetchColumn();
} catch (Throwable) {}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>
<?php require_once __DIR__ . '/includes/admin_sidebar.php'; ?>

<!-- Mobile sidebar toggle -->
<div class="d-lg-none mb-3">
  <button class="btn btn-sm btn-outline-secondary" data-toggle-sidebar>
    <i class="fa-solid fa-bars me-1"></i>Menu
  </button>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <img src="assets/images/ndmulogo.svg" alt="NDMU" width="32" height="32" style="object-fit:contain">
      <h1 class="h4 fw-bold mb-0">Admin Dashboard</h1>
    </div>
    <div class="text-muted small">Welcome back, <?= e((string)$me['name']) ?></div>
  </div>
</div>

<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="row g-3 fade-up">
  <div class="col-md-4">
    <div class="card stat-card card-lift">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon icon-navy">
            <i class="fa-solid fa-users"></i>
          </div>
          <div>
            <div class="stat-label">Users</div>
            <div class="stat-value" data-count="<?= (int)$stats['users'] ?>"><?= (int)$stats['users'] ?></div>
          </div>
        </div>
        <a href="admin_users.php" class="small fw-semibold d-block mt-2" style="color:var(--ndmu-gold);">Manage users &rarr;</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card stat-card card-lift">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon icon-emerald">
            <i class="fa-solid fa-building"></i>
          </div>
          <div>
            <div class="stat-label">Facilities</div>
            <div class="stat-value" data-count="<?= (int)$stats['facilities'] ?>"><?= (int)$stats['facilities'] ?></div>
          </div>
        </div>
        <a href="admin_facilities.php" class="small fw-semibold d-block mt-2" style="color:var(--ndmu-gold);">Manage facilities &rarr;</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card stat-card card-lift">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon icon-info">
            <i class="fa-solid fa-box-open"></i>
          </div>
          <div>
            <div class="stat-label">Items</div>
            <div class="stat-value" data-count="<?= (int)$stats['items'] ?>"><?= (int)$stats['items'] ?></div>
          </div>
        </div>
        <a href="admin_items.php" class="small fw-semibold d-block mt-2" style="color:var(--ndmu-gold);">Manage items &rarr;</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card stat-card card-lift">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon icon-gold">
            <i class="fa-regular fa-calendar-days"></i>
          </div>
          <div>
            <div class="stat-label">Facility Bookings</div>
            <div class="stat-value" data-count="<?= (int)$stats['facility_bookings'] ?>"><?= (int)$stats['facility_bookings'] ?></div>
          </div>
        </div>
        <a href="admin_bookings.php" class="small fw-semibold d-block mt-2" style="color:var(--ndmu-gold);">View bookings &rarr;</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card stat-card card-lift">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="card-icon icon-danger">
            <i class="fa-solid fa-boxes-stacked"></i>
          </div>
          <div>
            <div class="stat-label">Item Bookings</div>
            <div class="stat-value" data-count="<?= (int)$stats['item_bookings'] ?>"><?= (int)$stats['item_bookings'] ?></div>
          </div>
        </div>
        <a href="admin_bookings.php" class="small fw-semibold d-block mt-2" style="color:var(--ndmu-gold);">View bookings &rarr;</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_sidebar_end.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
