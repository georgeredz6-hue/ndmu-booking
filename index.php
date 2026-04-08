<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (isLoggedIn()) {
    $role = (string)($_SESSION['user']['role'] ?? '');
    header('Location: ' . roleRedirectTarget($role));
    exit;
}

$settings = [
    'school_name' => 'Notre Dame of Marbel University',
    'address' => 'Marbel, Koronadal City, South Cotabato, Philippines',
];
try {
    $stmt = $pdo->query("SELECT `key`, `value` FROM system_settings WHERE `key` IN ('school_name','address')");
    foreach ($stmt->fetchAll() as $row) {
        $settings[(string)$row['key']] = (string)$row['value'];
    }
} catch (Throwable) {}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="ndmu-hero" style="background-image:url('assets/images/ndmubgp.jpg')">
  <div class="container text-center py-5">
    <img src="assets/images/ndmulogo.svg" alt="NDMU" class="hero-logo mb-4 fade-up">
    <h1 class="hero-title fade-up fade-up-delay-1"><?= e($settings['school_name']) ?></h1>
    <div class="hero-subtitle mb-3 fade-up fade-up-delay-2">Facility Booking System</div>
    <p class="hero-desc mb-4 fade-up fade-up-delay-2">
      Reserve campus facilities, borrow equipment, and track approvals -- all in one place.
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap fade-up fade-up-delay-3">
      <a class="btn btn-warning btn-lg" href="login.php">
        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Sign In
      </a>
      <a class="btn btn-outline-light btn-lg" href="register.php">Create Account</a>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-heading fade-up">What we offer</div>
      <h2 class="fade-up">Everything you need to manage campus bookings</h2>
    </div>

    <div class="row g-4">
      <div class="col-md-4 fade-up fade-up-delay-1">
        <div class="card card-lift h-100">
          <div class="card-body">
            <div class="card-icon icon-emerald mb-3">
              <i class="fa-regular fa-calendar-days"></i>
            </div>
            <h5 class="mb-2">Book a Facility</h5>
            <p class="text-muted mb-0">Reserve classrooms, halls, and campus venues with conflict checks and transparent approvals.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 fade-up fade-up-delay-2">
        <div class="card card-lift h-100">
          <div class="card-body">
            <div class="card-icon icon-info mb-3">
              <i class="fa-solid fa-box-open"></i>
            </div>
            <h5 class="mb-2">Borrow Equipment</h5>
            <p class="text-muted mb-0">Request campus equipment and supplies with availability checks and pickup/return schedules.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 fade-up fade-up-delay-3">
        <div class="card card-lift h-100">
          <div class="card-body">
            <div class="card-icon icon-gold mb-3">
              <i class="fa-regular fa-circle-check"></i>
            </div>
            <h5 class="mb-2">Track Approvals</h5>
            <p class="text-muted mb-0">Follow each approval step from Adviser through President with real-time notifications.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- How It Works -->
<section class="section-padding">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-heading fade-up">How it works</div>
      <h2 class="fade-up">Simple steps to book any facility</h2>
    </div>

    <div class="row g-4">
      <div class="col-md-3 fade-up fade-up-delay-1">
        <div class="text-center">
          <div class="card-icon icon-navy mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;font-size:1.25rem;">
            <strong>1</strong>
          </div>
          <h6 class="fw-bold">Create Account</h6>
          <p class="text-muted small mb-0">Register with your NDMU email for quick verification.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up fade-up-delay-2">
        <div class="text-center">
          <div class="card-icon icon-navy mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;font-size:1.25rem;">
            <strong>2</strong>
          </div>
          <h6 class="fw-bold">Submit Request</h6>
          <p class="text-muted small mb-0">Choose a facility or item and fill out the booking form.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up fade-up-delay-3">
        <div class="text-center">
          <div class="card-icon icon-navy mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;font-size:1.25rem;">
            <strong>3</strong>
          </div>
          <h6 class="fw-bold">Get Approved</h6>
          <p class="text-muted small mb-0">Your request goes through a transparent multi-step review.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up">
        <div class="text-center">
          <div class="card-icon icon-gold mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;font-size:1.25rem;">
            <strong>4</strong>
          </div>
          <h6 class="fw-bold">Use Facility</h6>
          <p class="text-muted small mb-0">Download your receipt and enjoy the reserved space.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Banner -->
<section class="section-padding bg-white">
  <div class="container">
    <div class="banner p-4 p-md-5 text-white fade-up" style="background-image:url('assets/images/ndmubgp.jpg')">
      <div class="banner-content">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div>
            <h2 class="fw-bold mb-1">Efficient. Transparent. Streamlined.</h2>
            <div style="color:rgba(255,255,255,0.6);">Learn how booking and approvals work at NDMU.</div>
          </div>
          <a class="btn btn-warning btn-lg flex-shrink-0" href="faq.php">
            Learn More <i class="fa-solid fa-arrow-right ms-2"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
