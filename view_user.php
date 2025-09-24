<?php
require_once(__DIR__ . '/models/UserModel.php');
$id = intval($_GET['id'] ?? 0);
$userModel = new UserModel();
$user = $userModel->findUserById($id);
$user = $user[0] ?? null;
if (!$user) { header("Location: list_users.php"); exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>View User</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"></head>
<body>
<div class="container" style="margin-top:30px;">
  <h3>View user</h3>
  <table class="table">
    <tr><th>ID</th><td><?php echo $user['id']; ?></td></tr>
    <tr><th>Name</th><td><?php echo htmlspecialchars($user['name']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
    <tr><th>Created</th><td><?php echo $user['created_at']; ?></td></tr>
  </table>
  <a href="list_users.php" class="btn btn-default">Back</a>
</div>
</body></html>
