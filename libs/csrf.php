<?php
// libs/csrf.php
// Simple CSRF helper. Safe to include multiple times.

if (session_status() === PHP_SESSION_NONE) {
    // Attempt to start session silently
    @session_start();
}

/**
 * Ensure a token exists for current session and return it.
 * @return string
 */
function csrf_get_token() {
    if (!isset($_SESSION['csrf_token'])) {
        // 32-byte random, hex encoded
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden input tag for forms
 * @return string
 */
function csrf_input_tag() {
    $token = htmlspecialchars(csrf_get_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Validate token from a request (POST/GET)
 * @param string $token
 * @return bool
 */
function csrf_validate_token($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }
    if (!is_string($token) || $token === '') return false;
    if (!isset($_SESSION['csrf_token'])) return false;
    // Use hash_equals to mitigate timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Optional helper to regenerate token (call after successful login if desired)
 */
function csrf_regenerate_token() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
