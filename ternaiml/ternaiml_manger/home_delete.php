<?php
require "config.php";

// 确保接收到有效的 ID
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];

    // 构建 SQL 删除语句
    $sql = "DELETE FROM home WHERE home_id = $id";

    // 执行删除操作
    if (mysqli_query($conn, $sql)) {
        echo "删除成功！";
    } else {
        echo "删除失败: " . mysqli_error($conn);
    }
} else {
    echo "无效的 ID。";
}
