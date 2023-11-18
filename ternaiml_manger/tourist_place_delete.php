<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 获取从 AJAX 传递过来的 touristId
    $touristId = isset($_POST["touristId"]) ? $_POST["touristId"] : null;

    if ($touristId !== null) {
        // 进行删除操作，这里假设 tourists_place 表中有 tourist_id 字段
        $deleteQuery = "DELETE FROM tourists_place WHERE tourist_id = $touristId";
        $result = $conn->query($deleteQuery);

        if ($result) {
            echo "删除成功";
        } else {
            echo "删除失败：" . $conn->error;
        }
    } else {
        echo "无效的请求";
    }
} else {
    echo "仅允许 POST 请求";
}

$conn->close();
