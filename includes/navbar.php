<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

$user = getCurrentUser();
$unreadCount = 0;
if ($user) {
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([(int)$user['id']]);
        $unreadCount = (int)$stmt->fetchColumn();
    } catch (Throwable) {
        $unreadCount = 0;
    }
}

// Determine current page for active state
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<nav class="navbar navbar-expand-lg navbar-dark ndmu-navbar">
  <div class="container">
    <a class="navbar-brand" href="<?= $user ? 'student_dashboard.php' : 'index.php' ?>">
      <img src="assets/images/ndmulogo.svg" alt="NDMU" width="32" height="32">
      <span>NDMU Booking</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ndmuNav" aria-controls="ndmuNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="ndmuNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'faq.php' ? 'active' : '' ?>" href="faq.php">FAQ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
        </li>

        <?php if ($user): ?>
          <?php if ((string)$user['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link <?= str_starts_with($currentPage, 'admin') ? 'active' : '' ?>" href="admin_panel.php">
                <i class="fa-solid fa-screwdriver-wrench me-1"></i>Admin
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>" href="profile.php">Profile</a>
          </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <?php if ($user): ?>
          <li class="nav-item">
            <a class="nav-link position-relative" href="notifications.php" aria-label="Notifications">
              <i class="fa-solid fa-bell"></i>
              <span id="notifBadge"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge"
                    style="<?= $unreadCount ? '' : 'display:none;' ?>">
                <?= (int)$unreadCount ?>
              </span>
            </a>
          </li>
          <li class="nav-item d-none d-lg-block">
            <span class="navbar-text small" style="color:rgba(255,255,255,0.5);"><?= e((string)$user['name']) ?></span>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-sm btn-outline-light" href="login.php">Sign In</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-warning" href="register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
