<?php
require_once(__DIR__ . '/models/UserModel.php');
require_once(__DIR__ . '/libs/csrf.php');

$userModel = new UserModel();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list_users.php");
    exit;
}

// Nếu submit POST để xác nhận xoá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!csrf_validate_token($csrf)) {
        die("CSRF token không hợp lệ!");
    }

    $userModel->deleteUserById($id);
    header("Location: list_users.php");
    exit;
}

// Lấy thông tin user để hiển thị confirm
$user = $userModel->findUserById($id);
if (!$user) {
    header("Location: list_users.php");
    exit;
}
$user = $user[0];
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
        <li><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></li>
        <li><strong>Username:</strong> <?php echo htmlspecialchars($user['name']); ?></li>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
      </ul>
      <form method="POST">
        <?php echo csrf_input_tag(); ?>
        <button type="submit" class="btn btn-danger">Xóa</button>
        <a href="list_users.php" class="btn btn-default">Hủy</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
