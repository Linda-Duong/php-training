<?php
// libs/csrf.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_generate_token($namespace = 'csrf') {
    if (!isset($_SESSION[$namespace])) {
        $_SESSION[$namespace] = [];
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION[$namespace][$token] = time() + 1800; // 30 phút
    // Xóa token hết hạn
    foreach ($_SESSION[$namespace] as $t => $exp) {
        if ($exp < time()) unset($_SESSION[$namespace][$t]);
    }
    return $token;
}

function csrf_validate_token($token, $namespace = 'csrf') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($token)) return false;
    if (empty($_SESSION[$namespace])) return false;
    if (!isset($_SESSION[$namespace][$token])) return false;
    unset($_SESSION[$namespace][$token]); // dùng 1 lần
    return true;
}

function csrf_input_tag($namespace = 'csrf') {
    $token = csrf_generate_token($namespace);
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($token, ENT_QUOTES).'">';
}
