<?php
// place_save.php

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regionId = isset($_POST['region_id']) ? intval($_POST['region_id']) : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $original_image_url = isset($_POST['original_image_url']) ? $_POST['original_image_url'] : '';
    // Initialize $newImageUrl
    $newImageUrl = '';

    // 处理上传的新图片
    if (isset($_FILES['new_image'])) {
        $newImage = $_FILES['new_image'];

        // Check for errors during file upload
        if ($newImage['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => '文件上传错误', 'error_code' => $newImage['error']]);
            exit;
        }

        $uploadDir = __DIR__ . '/../place_img/';
        $uploadFile = $uploadDir . basename($newImage['name']);

        if (move_uploaded_file($newImage['tmp_name'], $uploadFile)) {
            // 更新 regions 表中的图片路径
            $relativePath = 'place_img/' . basename($newImage['name']);

            $updateImage = $conn->prepare("UPDATE regions SET image_url = ? WHERE region_id = ?");
            $updateImage->bind_param("si", $relativePath, $regionId);
            $updateImage->execute();

            // Check for successful database update
            if ($updateImage->affected_rows > 0) {
                $newImageUrl = $relativePath;
            } else {
                echo json_encode(['status' => 'error', 'message' => '数据库更新失败']);
                exit;
            }
        } else {
            $newImageUrl = isset($_POST['original_image_url']) ? $_POST['original_image_url'] : '';
        }
    }

    // 更新 regions 表中的其他字段
    $updateRegion = $conn->prepare("UPDATE regions SET name = ?, description = ? WHERE region_id = ?");
    $updateRegion->bind_param("ssi", $name, $description, $regionId);

    // Check for successful database update
    if ($updateRegion->execute()) {
        echo json_encode(['status' => 'success', 'message' => '保存成功', 'new_image_url' => $newImageUrl]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '数据库更新失败']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '无效的请求']);
}

$conn->close();
