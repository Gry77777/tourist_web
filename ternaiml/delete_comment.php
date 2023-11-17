<?php
// delete_comment.php

// 连接数据库和启动会话等代码
require "config.php";
session_start();

// 获取评论 ID
$comment_id_to_delete = isset($_POST['comment_id']) ? $_POST['comment_id'] : null;

// 删除评论的代码
$deleteSql = "DELETE FROM region_comments WHERE comment_id = ?";
$deleteStmt = $conn->prepare($deleteSql);

if ($deleteStmt) {
    $deleteStmt->bind_param("i", $comment_id_to_delete);
    $deleteStmt->execute();

    // 检查是否成功删除
    if ($deleteStmt->affected_rows > 0) {
        // 返回 JSON 数据表示成功
        echo json_encode(array("status" => "success", "message" => "Comment deleted successfully"));
    } else {
        // 返回 JSON 数据表示未成功删除
        echo json_encode(array("status" => "error", "message" => "Comment not found or unable to delete"));
    }
} else {
    // 返回 JSON 数据表示删除过程中出错
    echo json_encode(array("status" => "error", "message" => "Error in comment deletion preparation: " . $conn->error));
}
