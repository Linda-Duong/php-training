<?php
require_once(__DIR__ . '/models/UserModel.php');
require_once(__DIR__ . '/libs/csrf.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * helper flash
 */
function flash_set($key, $msg) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['_flash'][$key] = $msg;
}
function flash_get_all() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

// instantiate model
$userModel = new UserModel();

// normalize id (from GET or POST)
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
} else {
    $id = intval($_GET['id'] ?? 0);
}

if ($id <= 0) {
    // invalid id -> redirect
    header("Location: list_users.php");
    exit;
}

// Handle POST: perform delete after CSRF validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!csrf_validate_token($csrf)) {
        // CSRF invalid
        flash_set('error', 'CSRF token không hợp lệ. Hành động bị chặn.');
        header("Location: list_users.php");
        exit;
    }

    // Optional protection: prevent deleting admin (id 1)
    if ($id === 1) {
        flash_set('error', 'Không thể xóa tài khoản admin.');
        header("Location: list_users.php");
        exit;
    }

    // If you have server-side session-based auth, you can prevent deleting current user here.
    // Example (if $_SESSION['user_id'] exists):
    // if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id) {
    //     flash_set('error', 'Bạn không thể xóa chính mình.');
    //     header("Location: list_users.php");
    //     exit;
    // }

    $ok = $userModel->deleteUserById($id);
    if ($ok) {
        flash_set('success', 'Xóa user thành công.');
    } else {
        flash_set('error', 'Xóa thất bại hoặc user không tồn tại.');
    }
    header("Location: list_users.php");
    exit;
}

// GET request: show confirm page
// Get user info. Support both return types: single assoc or array of rows.
$raw = $userModel->findUserById($id);

// Normalize $user to single assoc array or null
$user = null;
if ($raw === null) {
    $user = null;
} elseif (is_array($raw)) {
    // if returned as numeric-indexed array of rows: first element
    $firstKeys = array_keys($raw);
    if (is_int($firstKeys[0] ?? null)) {
        $user = $raw[0] ?? null;
    } else {
        // returned a single assoc row
        $user = $raw;
    }
} else {
    $user = null;
}

if (!$user) {
    // not found -> redirect
    flash_set('error', 'User không tồn tại.');
    header("Location: list_users.php");
    exit;
}

// Render confirm HTML
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Xóa User</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    body { background:#f5f5f5; }
    .confirm-box { max-width:600px; margin:50px auto; }
  </style>
</head>
<body>
<?php include 'views/header.php'; ?>

<div class="container confirm-box">
  <div class="panel panel-danger">
    <div class="panel-heading"><strong>Xác nhận xóa user</strong></div>
    <div class="panel-body">
      <p>Bạn có chắc chắn muốn xóa user sau đây?</p>
      <ul>
        <li><strong>ID:</strong> <?php echo htmlspecialchars($user['id'] ?? $id, ENT_QUOTES, 'UTF-8'); ?></li>
        <li><strong>Username:</strong> <?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></li>
      </ul>

      <form method="POST" style="display:inline-block;">
        <?php echo csrf_input_tag(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <button type="submit" class="btn btn-danger">Xóa</button>
      </form>

      <a href="list_users.php" class="btn btn-default">Hủy</a>
    </div>
  </div>
</div>
</body>
</html>
