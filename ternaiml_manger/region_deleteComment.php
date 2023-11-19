<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["commentId"])) {
    $commentId = $_POST["commentId"];

    // 使用参数化查询，防止 SQL 注入攻击
    $deleteSql = "DELETE FROM region_comments WHERE comment_id = ?";

    // 准备并绑定语句
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $commentId);

    // 执行语句
    $stmt->execute();

    // 获取删除的行数
    $affectedRows = $stmt->affected_rows;

    // 关闭语句
    $stmt->close();

    if ($affectedRows > 0) {
        echo json_encode(["status" => "success", "message" => "删除成功"]);
    } else {
        echo json_encode(["status" => "error", "message" => "删除失败：" . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "非法请求"]);
}

// 关闭连接
$conn->close();
