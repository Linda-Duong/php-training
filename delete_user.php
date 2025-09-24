<?php
require_once(__DIR__ . '/models/UserModel.php');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: list_users.php"); exit; }
$userModel = new UserModel();
$userModel->deleteUserById($id);
header("Location: list_users.php");
exit;
