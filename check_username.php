<?php
// 假设你已经连接到数据库并且$conn是你的数据库连接对象
require "config.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];

    // 查询数据库中是否已存在相同的用户名
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'exists'; // 用户名已存在
    } else {
        echo 'available'; // 用户名可用
    }
} else {
    echo 'Invalid request';
}
