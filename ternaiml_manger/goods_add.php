<?php
// goods_add.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 连接数据库
    require "config.php";

    // 获取POST请求中的数据
    $newName = $_POST['newName'];
    $newDescription = $_POST['newDescription'];

    // 处理图片上传
    $newImage = null;
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == UPLOAD_ERR_OK) {
        // 上传的文件信息
        $uploadFile = $_FILES['newImage'];
        $tmpPath = $uploadFile['tmp_name'];
        $fileName = $uploadFile['name'];

        // 保存上传文件到指定目录，这里假设保存到当前脚本所在目录的 uploads 文件夹中
        $targetPath = '../goods_img/' . $fileName;
        move_uploaded_file($tmpPath, $targetPath);

        // 在实际应用中，你可能需要保存文件路径到数据库中
        $newImage = $targetPath;
    }

    // 插入新商品信息
    $sql = "INSERT INTO goods (name, description, image) VALUES ('$newName', '$newDescription', '$newImage')";

    if ($conn->query($sql) === TRUE) {
        $response = array(
            'status' => 'success',
            'message' => '商品添加成功'
        );
        echo json_encode($response);
    } else {
        $response = array(
            'status' => 'error',
            'message' => '商品添加失败: ' . $conn->error
        );
        echo json_encode($response);
    }

    // 关闭数据库连接
    $conn->close();
} else {
    // 如果不是POST请求，返回错误信息
    $response = array(
        'status' => 'error',
        'message' => '无效的请求方法'
    );
    echo json_encode($response);
}
