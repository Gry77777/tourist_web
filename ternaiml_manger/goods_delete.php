<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取商品ID
    $goodsId = $_POST['goodsId'];

    // 执行删除操作
    $deleteSql = "DELETE FROM goods WHERE goods_id = $goodsId";
    $deleteResult = $conn->query($deleteSql);

    if ($deleteResult) {
        echo "删除成功";
    } else {
        echo "删除失败：" . mysqli_error($conn);
    }
}

$conn->close();
