<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commentId"])) {
    $commentId = $_POST["commentId"];

    // 使用参数化查询，防止 SQL 注入攻击
    $deleteSql = "DELETE FROM tourists_comments WHERE comment_id = ?";

    // 准备并绑定语句
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $commentId);

    // 执行语句
    $stmt->execute();

    // 获取结果
    $deleteResult = $stmt->get_result();

    if ($deleteResult) {
        echo json_encode(array("status" => "success", "message" => "删除成功"));
    } else {
        echo json_encode(array("status" => "error", "message" => "删除失败：" . mysqli_error($conn)));
    }

    // 关闭语句
    $stmt->close();
} else {
    echo json_encode(array("status" => "error", "message" => "非法请求"));
}

// 关闭连接
$conn->close();
