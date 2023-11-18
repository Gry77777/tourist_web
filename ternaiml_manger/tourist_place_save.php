<?php
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 从表单中获取数据
    $id = $_POST['id'];
    $name = $_POST['name'];

    // 检查是否上传了图像文件
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // 处理并保存图像文件
        $uploadDir = '../tourists_img/'; // 根据您的需求调整目录
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // 图像成功上传，使用新数据更新数据库
            $updateQuery = "UPDATE tourists_place SET name = '$name', img = '$uploadFile' WHERE tourist_id = $id";
            if ($conn->query($updateQuery) === TRUE) {
                echo "数据更新成功！";
            } else {
                echo "更新数据时出错：" . $conn->error;
            }
        } else {
            echo "上传图像时出错。";
        }
    } else {
        // 没有上传新图像，只更新文本数据
        $updateQuery = "UPDATE tourists_place SET name = '$name' WHERE tourist_id = $id";
        if ($conn->query($updateQuery) === TRUE) {
            echo "数据更新成功！";
        } else {
            echo "更新数据时出错：" . $conn->error;
        }
    }
} else {
    echo "无效的请求方法，请使用POST请求。";
}

$conn->close();
