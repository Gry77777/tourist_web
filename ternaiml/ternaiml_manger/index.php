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
    <title>金华旅游网站后台管理系统</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<script src="./js/jquery3.6.3.js"></script>
<script>
    $(document).ready(function() {
        var iframe = document.getElementById('contentFrame');
        // 监听 iframe 内容的变化并重新设置高度
        iframe.onload = function() {
            var extraHeight = 70; // 额外的高度
            iframe.style.height = (iframe.contentWindow.document.body.scrollHeight + extraHeight) + 'px';
        };
    });
</script>

<body>
    <div class="container">
        <div class="sidebar">
            <span style="font-size: 16px;"><em>金华旅游网站后台管理系统</em></span>
            <ul>
                <li data-url="home.php">首页管理</li>
                <li data-url="user_manger.php">用户信息管理</li>
                <li class="has-submenu">
                    金华地区管理
                    <ul>
                        <li data-url="place.php">地区预览图</li>
                        <li data-url="place_detail.php">地区详情</li>
                    </ul>
                </li>
                <li class="has-submenu">景点信息
                    <ul>
                        <li data-url="tourist_place.php">景点预览图</li>
                        <li data-url="tourist_detail.php">景点详情</li>
                    </ul>
                </li>
                <li class="has-submenu">商品信息
                    <ul>
                        <li data-url="goods.php">商品预览图</li>
                        <li data-url="goods_detail.php">商品详情</li>
                    </ul>
                </li>
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
                <iframe id="contentFrame" src="user_manger.php" frameborder="0"></iframe>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            // 导航功能
            $('.sidebar ul li[data-url]').click(function(e) {
                e.stopPropagation(); // 阻止事件传播到带有子菜单的父级 li
                var url = $(this).data('url');
                $('#contentFrame').attr('src', url);
            });

            $('.sidebar ul li.has-submenu').click(function(e) {
                e.stopPropagation();
                var submenu = $(this).children('ul');
                if (submenu.length) {
                    submenu.toggleClass('show');
                    $(this).toggleClass('collapsed');
                }
            });
            // 默认隐藏所有二级菜单
            $('.sidebar ul ul').removeClass('show');
        });
    </script>
</body>

</html>