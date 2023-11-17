<?php
require "config.php";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $raw_password = $_POST['password'];
    $password = password_hash($raw_password, PASSWORD_DEFAULT); // 对密码进行加密
    $sex = $_POST['sex'];

    // 获取上传的文件信息
    $file_name = $_FILES['img']['name'];
    $file_temp = $_FILES['img']['tmp_name'];
    $file_type = $_FILES['img']['type'];

    // 检查文件类型
    if ($file_type == 'image/jpeg' || $file_type == 'image/png') {
        // 将文件移动到指定目录
        $target_path = "img/" . basename($file_name);
        move_uploaded_file($file_temp, $target_path);
        // 保存到数据库
        $sex = ($_POST['sex'] === 'male') ? 0 : 1;
        $stmt = $conn->prepare("INSERT INTO users (username, password, sex, img) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $sex, $target_path);
        if ($stmt->execute()) {
            echo "<script>alert('注册成功');</script>";
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    } else {
        echo "只允许上传 JPEG 或 PNG 格式的图片";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/register.css">
</head>
<script src="./js/jquery3.6.3.js"></script>
<script src="./js/register.js"></script>


<body>
    <div class="container">
        <h2>欢迎注册古古旅游官网</h2>
        <form method="post" action="register.php" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="username">用户名:</label>
                <input type="text" id="username" name="username" required>
                <div id="username-message" style="color: red;"></div>
            </div>
            <div class="input-group">
                <label for="password">密码:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">确认密码:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <input type="hidden" name="password_hash" id="password_hash">
            <div class="input-group">
                <label for="img">Profile Picture:</label>
                <input type="file" id="img" name="img" accept="image/*" required onchange="previewImage(event)">
            </div>
            <div class="input-group" style="text-align: center;">
                <img id="preview" src="#" alt="Preview Image">
            </div>
            <div class="input-group">
                <div class="radio-container">
                    <span>性别:</span>
                    <input type="radio" id="male" name="sex" value="male" required>
                    <label for="male">男</label>
                    <input type="radio" id="female" name="sex" value="female" required>
                    <label for="female">女</label>
                </div>
            </div>
            <button type="submit" name="submit" onclick="hashPassword()">注册</button>
        </form>
        <p>已有帐号? <a href="login.php" class="register-link">点击登录</a></p>
    </div>

</body>

</html>