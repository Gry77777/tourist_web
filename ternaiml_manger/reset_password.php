<?php
// 引入数据库连接配置
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        // 重置密码为默认密码（示例中设置为 "123456"）
        $hashedPassword = password_hash("123456", PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = '$userId'";
        $result = $conn->query($updateQuery);

        if ($result) {
            // 密码重置成功
            echo json_encode(['status' => 'success', 'message' => '密码重置成功']);
        } else {
            // 密码重置失败
            echo json_encode(['status' => 'error', 'message' => '密码重置失败']);
        }
    } else {
        // 未提供用户 ID
        echo json_encode(['status' => 'error', 'message' => '未提供用户 ID']);
    }
} else {
    // 非法请求
    echo json_encode(['status' => 'error', 'message' => '非法请求']);
}
