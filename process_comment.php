<?php
// 引入数据库配置
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : null;
    $comment_text = isset($_POST['comment_text']) ? $_POST['comment_text'] : null;

    // Validate and sanitize input to prevent SQL injection
    $comment_text = htmlspecialchars($comment_text, ENT_QUOTES, 'UTF-8');

    // Assuming you have the user ID stored in a session variable
    $user_id = $_SESSION['user_id'];
    // Insert the new comment into the database
    $insertSql = "INSERT INTO region_comments (region_id, user_id, comment_text) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);

    if ($insertStmt) {
        $insertStmt->bind_param("iis", $region_id, $user_id, $comment_text);
        $insertStmt->execute();

        // Echo success message or updated comments
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error in comment insertion preparation']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
