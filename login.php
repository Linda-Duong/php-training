<?php
require_once(__DIR__ . '/models/UserModel.php');
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $user = $userModel->auth($username, $password);
        if ($user) {
            echo json_encode([
                "status" => "success",
                "user" => [
                    "id" => $user[0]['id'],
                    "name" => $user[0]['name'],
                    "email" => $user[0]['email']
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Sai username/email hoặc password!"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Vui lòng nhập đầy đủ thông tin!"
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body { background:#f5f5f5; }
        #loginbox { margin-top:50px; }
    </style>
</head>
<body>
<?php include 'views/header.php' ?>

<div class="container">
    <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size:80%; position:relative; top:-10px">
                    <a href="#">Forgot password?</a>
                </div>
            </div>

            <div style="padding-top:30px" class="panel-body">
                <form id="loginForm" class="form-horizontal" role="form">
                    
                    <div class="input-group" style="margin-bottom:25px">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="username or email">
                    </div>

                    <div class="input-group" style="margin-bottom:25px">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="password">
                    </div>

                    <div class="input-group" style="margin-bottom:25px">
                        <div class="checkbox">
                            <label><input type="checkbox" name="remember"> Remember Me</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 controls">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            Don't have an account!
                            <a href="form_user.php">Sign Up Here</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector("#loginForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    let res = await fetch("login.php", { method: "POST", body: fd });
    let data = await res.json();
    if (data.status === "success") {
        localStorage.setItem("user", JSON.stringify(data.user));
        alert("Login thành công!");
        window.location.href = "list_users.php";
    } else {
        alert(data.message);
    }
});
</script>

</body>
</html>
