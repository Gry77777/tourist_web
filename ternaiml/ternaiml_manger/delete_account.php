<?php
// 引入数据库连接配置
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $deleteQuery = "DELETE FROM users WHERE id = '$userId'";
        $result = $conn->query($deleteQuery);

        if ($result) {
            // 账号删除成功
            echo json_encode(['status' => 'success', 'message' => '账号删除成功']);
        } else {
            // 账号删除失败
            echo json_encode(['status' => 'error', 'message' => '账号删除失败']);
        }
    } else {
        // 未提供用户 ID
        echo json_encode(['status' => 'error', 'message' => '未提供用户 ID']);
    }
} else {
    // 非法请求
    echo json_encode(['status' => 'error', 'message' => '非法请求']);
}
