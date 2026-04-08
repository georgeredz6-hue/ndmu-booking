<?php
/**
 * Shared helper functions for NDMU Booking System.
 */
declare(strict_types=1);

/* ---------- Escaping ---------- */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ---------- Session / Auth ---------- */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user']['id']);
}

function getCurrentUser(): ?array
{
    return isLoggedIn() ? $_SESSION['user'] : null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/* ---------- CSRF ---------- */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireValidCsrfOrDie(): void
{
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!verifyCsrfToken($token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

/* ---------- Flash Messages ---------- */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirectWithMessage(string $url, string $type, string $message): void
{
    setFlash($type, $message);
    header('Location: ' . $url);
    exit;
}

/* ---------- Input ---------- */
function sanitizeInput(string $value): string
{
    return trim(strip_tags($value));
}

/* ---------- Role Helpers ---------- */
function roleRedirectTarget(string $role): string
{
    return match ($role) {
        'admin'          => 'admin_panel.php',
        'adviser'        => 'adviser.php',
        'dean'           => 'dean.php',
        'vp_admin'       => 'vp_admin.php',
        'avp_admin'      => 'avp_admin.php',
        'dsa_director'   => 'dsa_director.php',
        'ppss_director'  => 'ppss_director.php',
        'president'      => 'president.php',
        'staff'          => 'staff.php',
        'janitor'        => 'janitor_dashboard.php',
        'security'       => 'security_dashboard.php',
        default          => 'student_dashboard.php',
    };
}

function enforceCorrectDashboard(string $requiredRole): void
{
    requireLogin();
    $user = getCurrentUser();
    if ($user && (string)$user['role'] !== $requiredRole) {
        header('Location: ' . roleRedirectTarget((string)$user['role']));
        exit;
    }
}

function approvalChain(): array
{
    return ['adviser', 'dean', 'vp_admin', 'avp_admin', 'dsa_director', 'ppss_director', 'president', 'admin'];
}

/* ---------- UI Helpers ---------- */
function statusBadge(string $status): string
{
    $map = [
        'pending'        => ['bg-warning', 'Pending'],
        'approved'       => ['bg-info', 'In Review'],
        'fully_approved' => ['bg-success', 'Approved'],
        'rejected'       => ['bg-danger', 'Rejected'],
        'cancelled'      => ['bg-light', 'Cancelled'],
    ];
    $s = $map[$status] ?? ['bg-light', ucfirst($status)];
    return '<span class="badge ' . $s[0] . '">' . e($s[1]) . '</span>';
}

function approvalRoleBadge(string $role): string
{
    if ($role === '') return '<span class="badge bg-light">--</span>';
    return '<span class="badge bg-primary">' . e(ucwords(str_replace('_', ' ', $role))) . '</span>';
}
