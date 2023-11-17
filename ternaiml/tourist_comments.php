<?php
session_start();
require "config.php";

// 检查是否提供 tourist_id
if (!isset($_GET["tourist_id"])) {
    echo "Please provide a tourist_id.";
    exit; // 退出脚本
}

$tourist_id = $_GET["tourist_id"];

// 处理用户提交评论
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment_text"]) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // 从 session 中获取用户 ID
    $comment_text = $_POST["comment_text"];
    // 将评论插入数据库
    $sql = "INSERT INTO tourists_comments (tourist_id, user_id, comment_text, created_at) VALUES ('$tourist_id', '$user_id', '$comment_text', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "Comment added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// 处理用户删除评论
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_comment"]) && isset($_SESSION['user_id'])) {
    $comment_id = $_POST["delete_comment"];
    $user_id = $_SESSION["user_id"]; // 从 session 中获取用户 ID

    // 删除评论
    $sql = "DELETE FROM tourists_comments WHERE comment_id = '$comment_id' AND user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Comment deleted successfully";
    } else {
        echo "Error deleting comment: " . $conn->error;
    }
}

// 显示评论
if (isset($_SESSION['user_id'])) {
    // 获取评论数据
    $sql = "SELECT * FROM tourists_comments WHERE tourist_id = '$tourist_id'";
    $result = $conn->query($sql);

    if ($result === false) {
        echo "Error: " . $conn->error;
    } else {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = getUsername($conn, $row["user_id"]);

                // 显示评论和删除按钮（仅对当前用户的评论显示删除按钮）
                echo "<div class='comment'>";
                echo "<p><strong style='font-size: larger;'>$username:</strong> <span class='comment-time'>： " . $row["created_at"] . "</span></p>";
                echo "<p>" . $row["comment_text"] . "</p>";
                // 显示删除按钮
                if ($row["user_id"] == $_SESSION["user_id"]) {
                    echo "<form method='post' action='tourist_comments.php'>";
                    echo "<input type='hidden' name='delete_comment' value='" . $row["comment_id"] . "'>";
                    echo "<button type='submit' class='delete-comment' data-comment-id='" . $row["comment_id"] . "'>删除评论</button>";
                    echo "</form>";
                }

                echo "</div>";
            }
        } else {
            echo "No comments yet.";
        }
    }
} else {
    echo "<p class='login-prompt'>请登录后查看评论和发表评论</p>";

}

// 获取用户名函数
function getUsername($conn, $user_id)
{
    $sql = "SELECT username FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["username"];
    } else {
        return "Unknown User";
    }
}

// 关闭数据库连接
$conn->close();
