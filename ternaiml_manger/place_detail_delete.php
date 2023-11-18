<?php
// 获取 POST 请求中的参数
require "config.php";
$introductionId = $_POST['introductionId'];

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 执行删除操作
$sql = "DELETE FROM introductions WHERE id=$introductionId";

if ($conn->query($sql) === TRUE) {
    $response = array(
        'status' => 'success',
        'message' => '删除成功'
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => '删除失败: ' . $conn->error
    );
}

// 关闭数据库连接
$conn->close();

// 将响应转换为 JSON 格式并输出
echo json_encode($response);
