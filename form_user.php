<?php
require_once(__DIR__ . '/models/UserModel.php');
require_once(__DIR__ . '/libs/csrf.php');

$userModel = new UserModel();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!csrf_validate_token($csrf)) {
        $message = "<div class='alert alert-danger'>Invalid CSRF token!</div>";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email    = trim($_POST['email'] ?? '');

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
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
      body { background:#f5f5f5; }
      #signupbox { margin-top:50px; }
      .panel-title { font-weight:bold; }
  </style>
</head>
<body>
<?php include 'views/header.php'; ?>

<div class="container">
  <div id="signupbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <div class="panel-title">Sign Up</div>
      </div>
      <div class="panel-body" style="padding-top:30px">
        <?php echo $message; ?>
        <form method="POST">
          <?php echo csrf_input_tag(); ?>

          <div class="input-group" style="margin-bottom:25px">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input class="form-control" name="username" placeholder="Username" required>
          </div>

          <div class="input-group" style="margin-bottom:25px">
            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
            <input class="form-control" name="email" placeholder="Email" type="email" required>
          </div>

          <div class="input-group" style="margin-bottom:25px">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input class="form-control" name="password" placeholder="Password" type="password" required>
          </div>

          <div class="form-group">
            <button class="btn btn-primary" type="submit">Register</button>
            <a href="login.php" class="btn btn-link">Back to Login</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
