<?php
require "config.php";
session_start();

// 假设用户信息存储在名为users的表中
$user_id = $_SESSION['user_id']; // 假设你在Session中存储了用户ID
// 使用参数化查询来避免 SQL 注入攻击
$sql = "SELECT username, img FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // 假设用户ID是整数类型
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    if ($result->num_rows > 0) {
        // 输出数据
        while ($row = $result->fetch_assoc()) {
            $response = array(
                'username' => $row["username"],
                'img' => $row["img"]
            );
            echo json_encode($response);
        }
    } else {
        echo "0 结果";
    }
} else {
    // 查询失败，处理错误
    echo "数据库查询失败: " . $conn->error;
}
$stmt->close();
$conn->close();
