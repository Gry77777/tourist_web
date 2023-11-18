<?php
// submit_comment.php

// 连接数据库和启动会话等代码
require "config.php";
session_start();

// 获取地区 ID 参数和评论文本
$region_id = isset($_POST['region_id']) ? $_POST['region_id'] : null;
$comment_text = isset($_POST['comment_text']) ? $_POST['comment_text'] : null;

// 获取用户信息
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// 插入新评论的代码
$insertSql = "INSERT INTO region_comments (region_id, user_id, user_name, comment_text) VALUES (?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);

if ($insertStmt) {
    $insertStmt->bind_param("iiss", $region_id, $user_id, $user_name, $comment_text);
    $insertStmt->execute();

    // 输出新评论的 HTML
    $timestamp = date("Y-m-d H:i:s"); // 假设 timestamp 是评论的时间
    echo "<div class='comment'>";
    echo "<p><strong>{$user_name}</strong> - {$timestamp}</p>";
    echo "<p>{$comment_text}</p>";
    echo "<button class='delete-button' data-comment-id='{$insertStmt->insert_id}'>Delete</button>";
    echo "</div>";
} else {
    die("Error in comment insertion preparation: " . $conn->error);
}
?>
