<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 获取要删除的游客ID
    $touristId = $_POST['tourist_id'];

    // 执行删除操作，这里假设你的主键列名是 'tourist_id'
    $deleteQuery = "DELETE FROM tourist_details WHERE tourist_id = $touristId";
    $result = $conn->query($deleteQuery);

    if ($result) {
        echo "success";
    } else {
        echo "error";
    }

    $conn->close();
} else {
    // 如果不是POST请求，返回错误
    echo "error";
}
