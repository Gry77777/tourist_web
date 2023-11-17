<?php
require 'config.php'; // 确保正确引入了数据库配置

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regionId = isset($_POST['region_id']) ? intval($_POST['region_id']) : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $introductions = isset($_POST['introductions']) ? $_POST['introductions'] : [];

    // 更新 regions 表
    $updateRegion = $conn->prepare("UPDATE regions SET name = ?, description = ? WHERE region_id = ?");
    $updateRegion->bind_param("ssi", $name, $description, $regionId);
    $updateRegion->execute();

    // 更新 region_introductions 表
    foreach ($introductions as $intro) {
        $title = isset($intro['title']) ? $intro['title'] : '';
        $content = isset($intro['content']) ? $intro['content'] : '';
        // 这里假设更新操作
        $updateIntro = $conn->prepare("UPDATE region_introductions SET content = ? WHERE region_id = ? AND title = ?");
        $updateIntro->bind_param("sis", $content, $regionId, $title);
        $updateIntro->execute();
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();
