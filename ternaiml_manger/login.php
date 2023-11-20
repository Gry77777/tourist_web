<?php
require "config.php";

session_start(); // Start the session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 处理登录逻辑
    $username = $_POST["username"];
    $password = $_POST["password"];
    $loginErrors = array();

    // 检测用户名是否存在
    $checkUsernameQuery = "SELECT *, COALESCE(is_superadmin, 0) AS is_superadmin FROM admin WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);

    if ($result->num_rows == 0) {
        $loginErrors[] = "用户名不存在。";
    } else {
        // 验证密码
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user["password"])) {
            $loginErrors[] = "密码不正确。";
        } else {
            $isSuperadmin = $user["is_superadmin"];

            // 登录成功，判断是否是超级管理员
            if ($isSuperadmin == "1") {
                // 是超级管理员，将admin_id存入session
                $_SESSION['admin_id'] = $user['admin_id'];
                header("location: index.php");
                exit; // 终止脚本执行
            } else {
                $loginErrors[] = "您无权进入管理系统,等待超级管理员处理您的申请";
            }
        }
    }

    // 如果有错误，输出错误消息
    if (!empty($loginErrors)) {
        foreach ($loginErrors as $error) {
            echo '<script>alert("' . $error . '");</script>';
        }
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
    <script src="./js/jquery3.6.3.js"></script>
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
        <p>没有账户？<a href="register.php">在这里注册申请</a>。</p>
    </div>

    <script>
        // 用于检测用户名和密码长度的 jQuery 代码
    </script>
</body>

</html>