<?php
// 在需要验证用户登录状态的页面顶部，先启用Session
session_start();
// 检查用户是否已登录，如果未登录则重定向到登录页面
if (!isset($_SESSION['username'])) {
    $loggedIn = false;
} else {
    $loggedIn = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/index.css">
    <script src="./js/jquery3.6.3.js"></script>
    <script src="./js/index.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (!$loggedIn) { ?>
                $('.menu li').click(function() {
                    if ($(this).data('url') !== 'index.php') {}
                });
            <?php } ?>
        });
    </script>
</head>

<body>
    <div class="container">
        <!-- 头部 -->
        <div class="top">
            <div class="left">
                <span>
                    欢迎访问金华旅游网
                </span>
            </div>
            <div class="right" id="userSection">
                <span>
                    <?php
                    if ($loggedIn) {
                        echo '<a href="profile.php">' . $_SESSION['username'] . '</a>';
                        echo '<img src="' . $_SESSION['img'] . '" alt="User Image" style="width: 25px; height: 25px; border-radius: 15px;">';
                        echo '<form action="logout.php" method="post" style="display: inline;">
                    <input type="submit" value="退出登录">
                    </form>';
                    } else {
                        echo '<a href="login.php">登录</a>/<a href="register.php">注册</a>';
                    }
                    ?>
                </span>
            </div>
        </div>
        <!-- 大图片的显示 -->
        <div class="banner">
            <p>
                金华旅游足迹网
            </p>
            <div class="search">
                <input type="text" id="searchInput" placeholder="输入搜索内容">
                <button id="searchButton">点击搜索</button>
            </div>
            <span>
                服务热线:<em><a href="" style="display: inline-block;">400-1234-5678</a>
                </em>
            </span>
        </div>
        <!-- 导航栏 -->
        <div class="menu">
            <ul>
                <li data-url="home.php">
                    <img src="image/home.png" alt="Home Icon"> <!-- 请替换 "home-icon.png" 为实际的图片文件名 -->
                    网站首页
                </li>
                <li data-url="place.php">
                    <img src="image/flight.png" alt="Place Icon"> <!-- 请替换 "place-icon.png" 为实际的图片文件名 -->
                    金华各地
                </li>
                <li data-url="tourists.php">
                    <img src="image/navigate.png" alt="Tourists Icon"> <!-- 请替换 "tourists-icon.png" 为实际的图片文件名 -->
                    金华景区
                </li>
                <li data-url="goods.php">
                    <img src="image/shopping.png" alt="Special Icon"> <!-- 请替换 "special-icon.png" 为实际的图片文件名 -->
                    特产商品
                </li>
                <li data-url="personal.php">
                    <img src="image/user_big.png" alt="Personal Icon"> <!-- 请替换 "personal-icon.png" 为实际的图片文件名 -->
                    个人中心
                </li>
                <li data-url="inquiry.php">
                    <img src="image/robot.png" alt="Inquiry Icon"> <!-- 请替换 "inquiry-icon.png" 为实际的图片文件名 -->
                    在线咨询
                </li>
            </ul>

        </div>
        <div class="lunbo">
            <img src="./img/banner.jpg" alt="" style="width:100%">
        </div>
        <!-- 中心的iframe框架 -->
        <div id="iframe-container">
            <iframe src="home.php" frameborder="0" id="myIframe"></iframe>
        </div>


        <!-- 网页底部 -->
        <div id="footer">
            <p>&copy; 2023 Your Website. All Rights Reserved. | Designed by <a href="https://www.example.com" target="_blank">GRY</a></p>
        </div>
    </div>
</body>


<script>
    $(document).ready(function() {
        $('#myIframe').on('load', function() {
            var iframeHeight = $(this).contents().find('body').height();
            $('#myIframe').height(iframeHeight + 50);
        });
    });
</script>

</html>