<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list_users.php');
    exit;
}

$rows = $userModel->findUserById($id);
if (empty($rows)) {
    header('Location: list_users.php');
    exit;
}
$user = $rows[0];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>View user</title>
    <?php include 'views/meta.php' ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
<?php include 'views/header.php' ?>
<div class="container" style="margin-top:30px;">
    <div class="panel panel-default" style="max-width:700px;">
        <div class="panel-heading"><strong>View User</strong></div>
        <div class="panel-body">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Fullname:</strong> <?php echo htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($user['type'], ENT_QUOTES, 'UTF-8') ?></p>
            <a href="list_users.php" class="btn btn-default">Back</a>
            <a href="form_user.php?id=<?php echo urlencode($user['id']); ?>" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
</body>
</html>
