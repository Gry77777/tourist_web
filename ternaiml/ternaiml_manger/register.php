<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 处理注册逻辑
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    $errors = array();

    // 检测用户名是否已存在
    $checkUsernameQuery = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);
    if ($result->num_rows > 0) {
        $errors[] = "Username already exists.";
    }

    // 密码长度检测
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // 前端密码一致性验证
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // 如果有错误，输出错误消息
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "Error: $error<br>";
        }
    } else {
        // 使用 password_hash() 对密码进行哈希
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 插入数据到数据库
        $sql = "INSERT INTO admin (username, password) VALUES ('$username', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Register</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="./css/register.css">

</head>

<body>
    <div class="login-container">
        <h2>管理员注册</h2>
        <form id="registerForm" method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <div class="error-message" id="usernameError"></div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <div class="error-message" id="passwordError"></div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <script>
        // 检测用户名是否已存在
        $(document).ready(function() {
            $('#username').on('input', function() {
                var username = $(this).val();
                var usernameError = $('#usernameError');
                $.ajax({
                    type: 'POST',
                    url: 'check_username.php', // 替换为实际的检测用户名是否存在的 PHP 文件
                    data: {
                        'username': username
                    },
                    success: function(response) {
                        if (response === 'exists') {
                            usernameError.text('Username already exists.');
                        } else {
                            usernameError.text('');
                        }
                    }
                });
            });

            // 实时验证密码长度
            $('#password').on('input', function() {
                var password = $(this).val();
                var passwordError = $('#passwordError');
                if (password.length === 0) {
                    passwordError.text(''); // 密码为空时清空错误消息
                } else if (password.length < 6) {
                    passwordError.text('Password must be at least 6 characters long.');
                } else {
                    passwordError.text('');
                }
            });
        });
    </script>
</body>

</html>