<?php
/**
 * Auth include — wraps the project's root-level auth helpers.
 * All page files reference includes/auth.php; this delegates
 * to the canonical auth implementation one level up.
 */
declare(strict_types=1);

// The root-level auth.php is actually a JS file in this codebase.
// Session management and auth helpers live in functions.php.
// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';
