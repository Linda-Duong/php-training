<?php
require_once 'models/UserModel.php';
$userModel = new UserModel();

$params = [];
if (!empty($_GET['keyword'])) {
    $params['keyword'] = $_GET['keyword'];
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <?php include 'views/meta.php' ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php include 'views/header.php'?>

<div class="container" style="margin-top:30px;">
    <!-- Kiểm tra login bằng localStorage -->
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
                        <td><?php echo htmlspecialchars($u['id']) ?></td>
                        <td><?php echo htmlspecialchars($u['name']) ?></td>
                        <td><?php echo htmlspecialchars($u['fullname']) ?></td>
                        <td><?php echo htmlspecialchars($u['email']) ?></td>
                        <td><?php echo htmlspecialchars($u['type']) ?></td>
                        <td>
                            <a href="form_user.php?id=<?php echo $u['id'] ?>" title="Update">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>
                            <a href="view_user.php?id=<?php echo $u['id'] ?>" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="delete_user.php?id=<?php echo $u['id'] ?>" 
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
