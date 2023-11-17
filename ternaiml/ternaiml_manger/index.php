<?php
// 确保 session 已启动
require "config.php";
session_set_cookie_params(0);
ini_set('session.gc_maxlifetime', 0);
session_start();
if (!isset($_SESSION['admin_id'])) {
    // 如果没有有效的admin_id，重定向到登录页面或其他适当的页面
    header("Location: login.php");
    exit();
}

// 处理退出操作
if (isset($_POST['logout'])) {
    // 销毁 session
    session_unset();
    session_destroy();
    // 重定向到登录页面或其他适当的页面
    header("Location: login.php");
    exit();
}

// 查询用户名
if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];
    $query = "SELECT username FROM admin WHERE admin_id = '$adminId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理系统</title>
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <span>旅游网站后台管理系统</span>
            <ul>
                <li data-url="user_manger.php">用户信息管理</li>
                <li data-url="Users.php">Users</li>
                <li data-url="place.php">各地区管理</li>
                <li data-url="Settings.php">Settings</li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="user-info">
                    <span>欢迎回来：<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
                </div>
                <form method="post" action="">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </div>
            <div class="main-content" id="mainContent">
                <iframe id="contentFrame" src="user_manger.php" frameborder="0" height="900px" ></iframe>
            </div>
        </div>

    </div>

    <script src="./js/jquery3.6.3.js"></script>
    <script>
        $(document).ready(function() {
            // 添加到列表项的点击事件
            $('.sidebar ul li').click(function() {
                // 获取data-url属性的值
                var url = $(this).data('url');
                // 更新iframe的src属性
                $('#contentFrame').attr('src', url);
            });
        });
    </script>
</body>

</html>