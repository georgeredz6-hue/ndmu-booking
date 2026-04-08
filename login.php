<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . roleRedirectTarget((string)($_SESSION['user']['role'] ?? '')));
    exit;
}

$flash = getFlash();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireValidCsrfOrDie();

    $email    = sanitizeInput($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare('SELECT id, full_name, email, role, password_hash, is_active FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if (!$u || (isset($u['is_active']) && (int)$u['is_active'] === 0) || !password_verify($password, (string)$u['password_hash'])) {
            $error = 'Invalid email or password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'    => (int)$u['id'],
                'name'  => (string)$u['full_name'],
                'email' => (string)$u['email'],
                'role'  => (string)$u['role'],
            ];
            $_SESSION['last_activity'] = time();

            if ($remember) {
                setcookie(session_name(), session_id(), [
                    'expires'  => time() + 60 * 60 * 24 * 7,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]);
            }

            header('Location: ' . roleRedirectTarget((string)$u['role']));
            exit;
        }
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid auth-page" style="background-image:url('assets/images/ndmubgp.jpg')">
  <div class="row min-vh-100">
    <!-- Left visual panel (desktop only) -->
    <div class="col-lg-6 d-none d-lg-flex ndmu-split-left align-items-end" style="background-image:url('assets/images/ndmubgp.jpg')">
      <div class="p-5" style="position:relative; z-index:5; background:linear-gradient(0deg, rgba(0,0,0,0.45) 0%, transparent 80%);">
        <h2 class="fw-bold mb-2" style="color:#ffffff !important; text-shadow:0 2px 10px rgba(0,0,0,0.7); font-size:1.75rem;">Notre Dame of Marbel University</h2>
        <p class="h5 mb-0" style="color:#ffffff !important; opacity:0.85; text-shadow:0 1px 6px rgba(0,0,0,0.6);">Facility Booking System</p>
      </div>
    </div>

    <!-- Right form panel -->
    <div class="col-lg-6 auth-form-side">
      <div class="auth-form-wrapper">
        <?php if ($flash): ?>
          <div class="alert alert-<?= e($flash['type']) ?> fade-up"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alert-danger fade-up"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="d-flex align-items-center gap-3 mb-4 fade-up">
          <img src="assets/images/ndmulogo.png" alt="NDMU" width="52" height="52" style="object-fit:contain">
          <div>
            <div class="fw-bold fs-5" style="color:var(--ndmu-navy);">Welcome back</div>
            <div class="text-muted small">Sign in to your account</div>
          </div>
        </div>

        <form method="post" class="fade-up fade-up-delay-1">
          <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control form-control-lg" type="email" name="email" required
                   value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email"
                   placeholder="you@ndmu.edu.ph">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input id="loginPassword" class="form-control form-control-lg" type="password" name="password"
                     required autocomplete="current-password" placeholder="Enter your password">
              <button class="btn btn-outline-secondary" type="button" data-toggle-password="#loginPassword" aria-label="Show/Hide password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" <?= !empty($_POST['remember']) ? 'checked' : '' ?>>
              <label class="form-check-label small" for="rememberMe">Remember me</label>
            </div>
            <a class="small fw-semibold" href="forgot_password.php" style="color:var(--ndmu-gold);">Forgot password?</a>
          </div>

          <button class="btn btn-warning btn-lg w-100 fw-semibold mb-3">
            Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
          </button>

          <div class="text-center">
            <span class="text-muted small">Don't have an account?</span>
            <a class="small fw-semibold" href="register.php" style="color:var(--ndmu-navy);">Create one</a>
          </div>

          <div class="text-muted small text-center mt-3" style="color:var(--ndmu-text-muted);">
            You will be redirected to your assigned role's dashboard automatically.
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
