<?php
require "config.php";
session_start();
session_set_cookie_params(0, '/', 'localhost', false, true);
ini_set('session.gc_maxlifetime', 300);

// 保存登录前的页面 URL
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // 查询数据库中是否存在匹配的用户名和密码
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // 登录成功，从数据库中获取用户头像路径
            $img = $user['img'];
            $userId = $user['id'];
            // 将用户名和头像路径存储在会话中
            $_SESSION['username'] = $username;
            $_SESSION['img'] = $img;
            $_SESSION['user_id'] = $userId;
            // 跳转回登录前的页面
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('密码错误！');</script>";
        }
    } else {
        echo "<script>alert('用户不存在，请注册后登录');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="./css/login.css">

<body>
    <div class="container">
        <h2>欢迎登录</h2>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <input type="submit" value="登录">
        </form>
        <p>没有账号? <a href="register.php" class="register-link">点击这里</a> | <a href="index.php" class="guest-link">不想注册？游客登录</a></p>

    </div>
</body>

</html>