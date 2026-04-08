<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$user = getCurrentUser();
$flash = getFlash();

$settings = [
    'school_name' => 'Notre Dame of Marbel University',
    'address' => 'Marbel, Koronadal City, South Cotabato, Philippines',
    'email' => 'admin@ndmu.edu.ph',
    'phone' => '(000) 000-0000',
];
try {
    $stmt = $pdo->query("SELECT `key`, `value` FROM system_settings WHERE `key` IN ('school_name','address','email','phone')");
    foreach ($stmt->fetchAll() as $row) {
        $settings[(string)$row['key']] = (string)$row['value'];
    }
} catch (Throwable) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfOrDie();

    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectWithMessage('contact.php', 'danger', 'Please complete all fields with a valid email.');
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())');
        $stmt->execute([$name, $email, $subject, $message]);
    } catch (Throwable) {
        redirectWithMessage('contact.php', 'danger', 'Unable to send message right now. Please try again later.');
    }

    $adminEmail = $settings['email'] ?: 'admin@ndmu.edu.ph';
    @mail($adminEmail, "[NDMU Booking] {$subject}", "From: {$name} <{$email}>\n\n{$message}");

    redirectWithMessage('contact.php', 'success', 'Your message has been received. We will get back to you within 24 hours.');
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<!-- Page Banner -->
<div class="page-banner">
  <div class="container">
    <div class="d-flex align-items-center gap-3">
      <img src="assets/images/ndmulogo.png" alt="NDMU" width="48" height="48" style="object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
      <div>
        <h1 class="h2 fw-bold mb-0">Contact Us</h1>
        <div class="text-white-50 small">We'd love to hear from you.</div>
      </div>
    </div>
  </div>
</div>

<div class="container pb-5">
  <?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> fade-up"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Contact Info -->
    <div class="col-lg-5 fade-up">
      <div class="card h-100">
        <div class="card-body">
          <h2 class="h5 fw-bold mb-4" style="color:var(--ndmu-navy);">Contact Information</h2>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="card-icon icon-navy flex-shrink-0" style="width:40px;height:40px;">
              <i class="fa-solid fa-location-dot" style="font-size:0.875rem;"></i>
            </div>
            <div>
              <div class="fw-semibold small">Address</div>
              <div class="text-muted small"><?= e($settings['address']) ?></div>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="card-icon icon-navy flex-shrink-0" style="width:40px;height:40px;">
              <i class="fa-solid fa-phone" style="font-size:0.875rem;"></i>
            </div>
            <div>
              <div class="fw-semibold small">Phone</div>
              <div class="text-muted small"><?= e($settings['phone']) ?></div>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <div class="card-icon icon-navy flex-shrink-0" style="width:40px;height:40px;">
              <i class="fa-solid fa-envelope" style="font-size:0.875rem;"></i>
            </div>
            <div>
              <div class="fw-semibold small">Email</div>
              <div class="text-muted small"><?= e($settings['email']) ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="col-lg-7 fade-up fade-up-delay-1">
      <div class="card">
        <div class="card-body">
          <h2 class="h5 fw-bold mb-4" style="color:var(--ndmu-navy);">Send a Message</h2>
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" value="<?= e($user['name'] ?? '') ?>" required placeholder="Your full name">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required placeholder="you@example.com">
              </div>
              <div class="col-12">
                <label class="form-label">Subject</label>
                <input class="form-control" name="subject" required placeholder="What is this about?">
              </div>
              <div class="col-12">
                <label class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="5" required placeholder="Write your message here..."></textarea>
              </div>
              <div class="col-12 d-flex gap-2">
                <button class="btn btn-warning fw-semibold">
                  <i class="fa-solid fa-paper-plane me-1"></i>Send Message
                </button>
                <a class="btn btn-outline-secondary" href="faq.php">View FAQ</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
