<?php
session_start();
require "config.php";
if (!isset($_SESSION['username'])) {
    $loggedIn = false;
} else {
    $loggedIn = true;
}

// 查询数据库获取目的地信息
$destinationId = isset($_GET['home_id']) ? (int)$_GET['home_id'] : 0;
$query = "SELECT * FROM home_detail WHERE id = $destinationId";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('数据库查询失败: ' . mysqli_error($conn));
}

// 获取目的地信息
$destination = mysqli_fetch_assoc($result);

// 关闭数据库连接
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>旅游目的地详情</title>
    <link rel="stylesheet" href="./css/home_detail.css">
</head>

<body>
    <div class="container">
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
                        if (isset($_SESSION['img'])) {
                            echo '<img src="' . $_SESSION['img'] . '" alt="User Image" style="width: 25px; height: 25px; border-radius: 15px;">';
                        } else {
                            // 处理$_SESSION['img']未定义的情况
                        }
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
        <header class="header">
            <h1>探索美丽的旅游目的地</h1>
        </header>

        <div class="main">
            <div class="image-container">
                <img src="./<?php echo $destination['image_url']; ?>" alt="目的地图片">
            </div>

            <div class="details">
                <h2><?php echo $destination['name']; ?></h2>
                <p class="description"><?php echo $destination['description']; ?></p>

                <ul class="additional-info">
                    <li><strong>推荐季节：</strong><?php echo $destination['recommended_season']; ?></li>
                    <li><strong>建议停留时间：</strong><?php echo $destination['suggested_stay_duration']; ?> 天</li>
                    <li><strong>主要景点：</strong><?php echo $destination['main_attractions']; ?></li>
                    <li><strong>特色活动：</strong><?php echo $destination['featured_activities']; ?></li>
                </ul>
            </div>
        </div>

    </div>

    <footer class="footer">
        <a href="back_to_home.php">返回首页</a>
    </footer>
</body>

</html>