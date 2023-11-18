<?php
// 引入数据库配置
require "config.php";
session_start();
$user_id = $_SESSION['user_id'];
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


//评论的实现功能
$commentSql = "SELECT comment_id, user_id, comment_text, timestamp FROM region_comments WHERE region_id = ?";
$commentStmt = $conn->prepare($commentSql);

// Check if the statement preparation is successful
if ($commentStmt) {
    $commentStmt->bind_param("i", $region_id);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();

    // Check if there are comments for the region
    $comments = [];
    if ($commentResult->num_rows > 0) {
        while ($commentRow = $commentResult->fetch_assoc()) {
            $comments[] = $commentRow;
        }
    }
} else {
    die("Error in comment statement preparation: " . $conn->error);
}

// Insert new comment
if (isset($_POST['submit_comment'])) {
    $comment_text = $_POST['comment_text'];

    // Validate and sanitize input to prevent SQL injection
    $comment_text = htmlspecialchars($comment_text, ENT_QUOTES, 'UTF-8');

    // Assuming you have the user ID stored in a session variable
    $user_id = $_SESSION['user_id'];

    // Query the username from the users table based on the user_id
    $usernameQuery = "SELECT username FROM users WHERE id = ?";
    $usernameStmt = $conn->prepare($usernameQuery);

    if ($usernameStmt) {
        $usernameStmt->bind_param("i", $user_id);
        $usernameStmt->execute();
        $usernameResult = $usernameStmt->get_result();

        if ($usernameResult->num_rows > 0) {
            $usernameRow = $usernameResult->fetch_assoc();
            $user_name = $usernameRow['username'];

            // Insert the new comment into the database
            $insertSql = "INSERT INTO region_comments (region_id, user_id, user_name, comment_text) VALUES (?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);

            // Check if the statement preparation is successful
            if ($insertStmt) {
                $insertStmt->bind_param("iiss", $region_id, $user_id, $user_name, $comment_text);
                $insertStmt->execute();

                // Redirect to the same page to display the new comment
                header("Location: place_detail.php?region_id=$region_id");
                exit();
            } else {
                die("Error in comment insertion preparation: " . $conn->error);
            }
        } else {
            die("User not found");
        }
    } else {
        die("Error in username statement preparation: " . $conn->error);
    }
}

//删除功能
// Check if the delete comment button is clicked
if (isset($_POST['delete_comment'])) {
    // Get the comment_id from the form submission
    $comment_id_to_delete = $_POST['comment_id'];

    // Delete the comment from the database
    $deleteSql = "DELETE FROM region_comments WHERE comment_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);

    if ($deleteStmt) {
        $deleteStmt->bind_param("i", $comment_id_to_delete);
        $deleteStmt->execute();

        // Redirect to the same page after deletion
        header("Location: place_detail.php?region_id=$region_id");
        exit();
    } else {
        die("Error in comment deletion preparation: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- 添加你的样式链接等 -->
    <title><?php echo $region_name; ?>详情</title>
</head>
<link rel="stylesheet" href="./css/place_detail.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
<script src="./js/jquery3.6.3.js"></script>

<body>
    <div class="container">
        <div class="top">
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

    <div class="comments">
        <h3>Comments</h3>
        <ul>
            <?php
            foreach ($comments as $comment) {
                // Fetch the username using user_id
                $userId = $comment['user_id'];
                $usernameQuery = "SELECT username FROM users WHERE id = ?";
                $usernameStmt = $conn->prepare($usernameQuery);

                if ($usernameStmt) {
                    $usernameStmt->bind_param("i", $userId);
                    $usernameStmt->execute();
                    $usernameResult = $usernameStmt->get_result();

                    if ($usernameResult->num_rows > 0) {
                        $usernameRow = $usernameResult->fetch_assoc();
                        $username = $usernameRow['username'];

                        // Display the comment with a delete button
                        echo "<li>";
                        echo "<p><strong>{$username}</strong> - {$comment['timestamp']}</p>";
                        echo "<p>{$comment['comment_text']}</p>";

                        // Delete button with a form
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="comment_id" value="' . $comment['comment_id'] . '">';
                        echo '<button type="submit" name="delete_comment" class="delete-button">Delete</button>';
                        echo '</form>';

                        echo "</li>";
                    }
                } else {
                    die("Error in username statement preparation: " . $conn->error);
                }
            }
            ?>
        </ul>
    </div>

    <!-- Form for submitting new comments -->
    <form method="post" action="">
        <label for="comment_text">Comment:</label>
        <textarea name="comment_text" rows="4" required></textarea>

        <button type="submit" name="submit_comment" class="submit-button">Submit Comment</button>
    </form>




    <!-- 底部代码 -->
    <footer>
        <p>&copy; 2023 Your Website. All rights reserved.</p>
    </footer>
</body>

</html>