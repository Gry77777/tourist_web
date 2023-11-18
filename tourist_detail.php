<?php
require "config.php";
session_start();
//判断用户是否有登录
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
    <title>Tourist Detail</title>
    <link rel="stylesheet" href="./css/tourist_detail.css">
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
                    echo '/<a href="register.php">注册</a>';
                }
                ?>
            </span>
        </div>
    </div>


    <div class="container">
        <?php
        // Get tourist_id from URL
        $tourist_id = $_GET['tourist_id'];
        $sql = "SELECT * FROM tourist_details WHERE tourist_id = $tourist_id";
        $result = $conn->query($sql);
        if ($result === false) {
            // Query failed
            echo "Error: " . $conn->error;
        } else {
            if ($result->num_rows > 0) {
                // Fetch the data
                $row = $result->fetch_assoc();
        ?>
                <div class="main">
                    <!-- Display the fetched data on the page -->
                    <h1><?php echo $row['title']; ?></h1>
                    <div class="image-container">
                        <img src="/<?php echo $row['image1']; ?>" alt="Image 1">
                        <img src="/<?php echo $row['image2']; ?>" alt="Image 2">
                        <img src="/<?php echo $row['image3']; ?>" alt="Image 3">
                    </div>
                    <p class="intro"><?php echo $row['introduction']; ?></p>
                    <p class="contact">Contact: <span><?php echo $row['phone']; ?></span></p>
                    <p class="opening-hours">Opening Hours: <span><?php echo $row['opening_hours']; ?></span></p>
                    <p class="ticket">Ticket: <span><?php echo $row['ticket']; ?></span></p>
                    <p class="transportation">Transportation: <span><?php echo $row['transportation']; ?></span></p>

                </div>
                <!-- Add more HTML elements as needed -->
        <?php
            } else {
                echo "No details found.";
            }
        }
        $conn->close();
        ?>
    </div>


    <div id="comments-container">
        <!-- 评论将在此显示 -->
    </div>

    <!-- 评论表单 -->
    <form id="comment-form" <?php if (!isset($_SESSION['user_id'])) echo 'onsubmit="return showLoginPrompt();"'; ?>>
        <input type="hidden" name="tourist_id" value="<?php echo $_GET['tourist_id']; ?>">
        <textarea name="comment_text" placeholder="发表评论"></textarea>
        <button type="submit">提交评论</button>
    </form>

    <script>
        function showLoginPrompt() {
            alert("请先登录后再发表评论。");
            return false; // 阻止表单提交
        }
        // 当页面加载完成时获取并显示评论
        $(document).ready(function() {
            loadComments();
        });

        // 处理评论表单提交
        $("#comment-form").submit(function(event) {
            event.preventDefault();
            submitComment();
        });

        // 获取并显示评论
        function loadComments() {
            var tourist_id = getParameterByName("tourist_id");
            $.ajax({
                type: "GET",
                url: "tourist_comments.php",
                data: {
                    tourist_id: tourist_id
                },
                success: function(data) {
                    $("#comments-container").html(data);
                }
            });
        }

        // 提交评论
        function submitComment() {
            var formData = $("#comment-form").serialize();
            $.ajax({
                type: "POST",
                url: "tourist_comments.php",
                data: formData,
                success: function(data) {
                    loadComments(); // 重新加载评论
                }
            });
        }

        // 处理删除评论
        $("#comments-container").on("click", "button.delete-comment", function(event) {
            event.preventDefault();
            var commentId = $(this).data("comment-id");

            $.ajax({
                type: "POST",
                url: "tourist_comments.php",
                data: {
                    delete_comment: commentId
                },
                success: function(data) {
                    loadComments(); // 重新加载评论
                }
            });
        });

        // 获取 URL 参数的函数
        function getParameterByName(name) {
            var url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    </script>



    <footer>
        &copy; 2023 Your Website
    </footer>


</body>

</html>