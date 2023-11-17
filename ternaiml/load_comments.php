<?php
// load_comments.php

// 连接数据库和启动会话等代码
require "config.php";
session_start();
// 获取地区 ID 参数
$region_id = isset($_GET['region_id']) ? $_GET['region_id'] : null;
// 查询评论的代码，按时间戳降序排列
$commentSql = "SELECT comment_id, user_id, user_name, comment_text, timestamp FROM region_comments WHERE region_id = ? ORDER BY timestamp DESC";
$commentStmt = $conn->prepare($commentSql);

if ($commentStmt) {
    $commentStmt->bind_param("i", $region_id);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();

    $comments = [];
    while ($commentRow = $commentResult->fetch_assoc()) {
        $comments[] = $commentRow;
    }

    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // 生成 HTML 输出
    foreach ($comments as $comment) {
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

                // 构建每条评论的 HTML 结构，包括用户名
                echo "<div class='comment'>";
                echo "<p><strong>{$username}</strong> - {$comment['timestamp']}</p>";
                echo "<p>{$comment['comment_text']}</p>";

                // 只有评论作者是当前登录用户时，才显示删除按钮
                if ($currentUserId == $userId) {
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='comment_id' value='{$comment['comment_id']}'>";
                    echo "<button type='submit' name='delete_comment' class='delete-button'>Delete</button>";
                    echo "</form>";
                }

                echo "</div>";
            } else {
                // Handle case where no username is found
                echo "<div class='comment'>";
                echo "<p>Error: User not found</p>";
                echo "</div>";
            }
        } else {
            // Handle case where username statement preparation fails
            echo "<div class='comment'>";
            echo "<p>Error in username statement preparation: " . $conn->error . "</p>";
            echo "</div>";
        }
    }
}
