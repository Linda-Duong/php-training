<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

$params = [];
$keywordRaw = '';
if (!empty($_GET['keyword'])) {
    $keywordRaw = trim($_GET['keyword']);
    $params['keyword'] = $keywordRaw;
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>List of users</title>
    <?php include 'views/meta.php' ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include 'views/header.php'?>

<div class="container" style="margin-top:30px;">
    <script>
    const user = JSON.parse(localStorage.getItem("user"));
    if (!user) {
        alert("Bạn chưa đăng nhập!");
        window.location.href = "login.php";
    } else {
        console.log("Đã login:", user.name);
    }
    </script>

    <?php if (!empty($users)) { ?>
        <div class="alert alert-warning" role="alert">
            List of users! <br>
            Hacker demo: 
            <code>list_users.php?keyword=ASDF%25%22%3BTRUNCATE+banks%3B%23%23</code>
        </div>

        <form class="form-inline" method="GET" action="list_users.php" style="margin-bottom:15px;">
            <div class="form-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search users" 
                       value="<?php echo htmlspecialchars($keywordRaw, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn btn-default">Search</button>
        </form>

        <table class="table table-striped table-bordered">
            <thead>
                <tr class="info">
                    <th>ID</th>
                    <th>Username</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?php echo htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?php echo htmlspecialchars($u['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?php echo htmlspecialchars($u['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="form_user.php?id=<?php echo urlencode($u['id']) ?>" title="Update">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            &nbsp;
                            <a href="view_user.php?id=<?php echo urlencode($u['id']) ?>" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            &nbsp;
                            <a href="delete_user.php?id=<?php echo urlencode($u['id']) ?>" 
                               onclick="return confirm('Xoá user này?')" title="Delete">
                                <i class="fa fa-eraser text-danger"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-dark" role="alert">
            Không có user nào!
        </div>
    <?php } ?>
</div>
</body>
</html>
