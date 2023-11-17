<?php
require "config.php";

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 处理登录逻辑
    $username = $_POST["username"];
    $password = $_POST["password"];

    $loginErrors = array();

    // 检测用户名是否存在
    $checkUsernameQuery = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);
    if ($result->num_rows == 0) {
        $loginErrors[] = "用户名不存在。";
    } else {
        // 验证密码
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user["password"])) {
            $loginErrors[] = "密码不正确。";
        } else {
            // 登录成功，将admin_id存入session
            $_SESSION['admin_id'] = $user['admin_id'];
        }
    }

    // 如果有错误，输出错误消息
    if (!empty($loginErrors)) {
        echo '<script>alert("登录失败，请检查用户名和密码。");</script>';
    } else {
        echo '<script>alert("登录成功！");</script>';
        header("location: index.php");
        // 可以添加登录成功后的跳转或其他逻辑
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="login-container">
        <h2>金华旅游网后台管理系统</h2>
        <form id="loginForm" method="post" action="">
            <div class="form-group">
                <label for="username">用户名:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">登录</button>
            </div>
        </form>
        <p>没有账户？<a href="register.php">在这里注册</a>。</p>
    </div>

    <script>
        // 用于检测用户名和密码长度的 jQuery 代码
    </script>
</body>

</html>