<?php
require_once(__DIR__ . '/models/UserModel.php');
$userModel = new UserModel();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username && $password && $email) {
        try {
            $userModel->createUser($username, $password, $email);
            header("Location: login.php?registered=1");
            exit;
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>" . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Vui lòng nhập đầy đủ thông tin!</div>";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="max-width:600px;margin-top:30px;">
  <?php echo $message; ?>
  <div class="panel panel-warning">
    <div class="panel-heading"><div class="panel-title">User form</div></div>
    <div class="panel-body">
      <form method="POST">
        <div class="form-group"><input class="form-control" name="username" placeholder="Username" required></div>
        <div class="form-group"><input class="form-control" name="email" placeholder="Email" type="email" required></div>
        <div class="form-group"><input class="form-control" name="password" placeholder="Password" type="password" required></div>
        <button class="btn btn-primary" type="submit">Submit</button>
        <a href="login.php" class="btn btn-link">Back to Login</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
