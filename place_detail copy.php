<?php
// 引入数据库配置
require "config.php";
session_start();
//判断用户是否有登录
if (!isset($_SESSION['username'])) {
    $loggedIn = false;
} else {
    $loggedIn = true;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// 获取地区 ID 参数
$region_id = isset($_GET['region_id']) ? $_GET['region_id'] : null;

// 查询对应地区的详细信息
$sql = "SELECT regions.name as region_name, region_introductions.title, region_introductions.content
        FROM regions
        JOIN region_introductions ON regions.region_id = region_introductions.region_id
        WHERE regions.region_id = ?";
$stmt = $conn->prepare($sql);

// 检查是否准备成功
if (!$stmt) {
    die("Error in statement preparation: " . $conn->error);
}

$stmt->bind_param("i", $region_id);
$stmt->execute();
$result = $stmt->get_result();

// 检查是否有查询结果
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // 在这里输出详细信息，例如：
    $region_name = $row['region_name'];
    $introduction_title = $row['title'];
    $introduction_content = $row['content'];

    // Fetch images for the region
    $imgSql = "SELECT image_url FROM region_images WHERE region_id = ?";
    $imgStmt = $conn->prepare($imgSql);

    // Check if the statement preparation is successful
    if (!$imgStmt) {
        die("Error in statement preparation: " . $conn->error);
    }

    $imgStmt->bind_param("i", $region_id);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();

    // Check if there are images for the region
    $images = [];
    if ($imgResult->num_rows > 0) {
        while ($imgRow = $imgResult->fetch_assoc()) {
            $images[] = $imgRow['image_url'];
        }
    }
} else {
    echo "<script>alert('暂无信息，请先浏览其他网页~')</script>";
    // 如果没有查询到结果，可以显示一个默认信息或者跳转回首页等操作
    // header("Location: index.php");
    // exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- 添加你的样式链接等 -->
    <title><?php echo $region_name; ?>详情</title>
    <link rel="stylesheet" href="./css/place_deatail copy.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
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
                    echo '<a href="login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']) . '">登录</a>';
                    echo "Encoded URI: " . $_SERVER['REQUEST_URI'];

                    echo '/<a href="register.php">注册</a>';
                }
                ?>
            </span>
        </div>
    </div>
    <div class="container">
        <div class="top_name">
            <h2><?php echo $region_name; ?></h2>
        </div>
        <div class="main">
            <div class="region-detail">
                <h3><?php echo $introduction_title; ?></h3>
                <p><?php echo $introduction_content; ?></p>

                <?php
                // Display images
                foreach ($images as $imageUrl) {
                    echo '<img src="' . $imageUrl . '" alt="Region Image">';
                }
                ?>
            </div>
        </div>
    </div>

    <?php if ($user_id) : ?>
        <div class="container">
            <!-- 其他内容... -->

            <!-- 评论区域 -->
            <div class="comments">
                <!-- 评论内容将由 JavaScript 动态加载 -->
            </div>

            <!-- 评论表单 -->
            <form id="commentForm">
                <label for="comment_text">Comment:</label>
                <textarea id="comment_text" name="comment_text" rows="4" required></textarea>
                <button type="submit" name="submit_comment" class="submit-button">Submit Comment</button>
            </form>
        </div>
    <?php else : ?>
        <div class="warn">
            <p>请登录后进行评论和查看评论。</p>
        </div>
    <?php endif; ?>

    <!-- 底部代码 -->
    <footer>
        <p>&copy; 2023 Your Website. All rights reserved.</p>
    </footer>

    <script>
        $(document).ready(function() {
            // 加载评论
            function loadComments() {
                $.ajax({
                    url: 'load_comments.php',
                    type: 'GET',
                    data: {
                        region_id: <?php echo $region_id; ?>
                    },
                    success: function(response) {
                        $('.comments').html(response);
                    },
                    error: function(error) {
                        console.log('Error loading comments:', error);
                    }
                });
            }

            // 提交评论
            $('#commentForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'submit_comment.php',
                    type: 'POST',
                    data: {
                        region_id: <?php echo $region_id; ?>,
                        comment_text: $('#comment_text').val()
                    },
                    success: function(response) {
                        loadComments(); // 重新加载评论
                        $('#comment_text').val(''); // 清空评论文本框
                    },
                    error: function(error) {
                        console.log('Error submitting comment:', error);
                    }
                });
            });

            // 删除评论
            $('.comments').on('click', '.delete-button', function() {
                var commentId = $(this).closest('.comment').find('input[name="comment_id"]').val();
                $.ajax({
                    url: 'delete_comment.php',
                    type: 'POST',
                    data: {
                        comment_id: commentId
                    },
                    success: function(response) {
                        loadComments(); // 重新加载评论
                    },
                    error: function(error) {
                        console.log('Error deleting comment:', error);
                    }
                });
            });
            // 初始化时加载评论
            loadComments();
        });
    </script>

</body>

</html>