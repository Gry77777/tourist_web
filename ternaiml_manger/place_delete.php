<?php
require 'config.php'; // 确保这里正确引入了数据库配置

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regionId = isset($_POST['region_id']) ? intval($_POST['region_id']) : 0;

    // 删除操作
    // 注意：删除 region 可能需要先删除相关联的 introductions 和 images
    $deleteIntro = $conn->prepare("DELETE FROM region_introductions WHERE region_id = ?");
    $deleteIntro->bind_param("i", $regionId);
    $deleteIntro->execute();

    // 同理，如果有 images 表
    // $deleteImages = $conn->prepare("DELETE FROM region_images WHERE region_id = ?");
    // $deleteImages->bind_param("i", $regionId);
    // $deleteImages->execute();

    $deleteRegion = $conn->prepare("DELETE FROM regions WHERE region_id = ?");
    $deleteRegion->bind_param("i", $regionId);
    $deleteRegion->execute();

    // 返回响应
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

// 关闭数据库连接
$conn->close();
