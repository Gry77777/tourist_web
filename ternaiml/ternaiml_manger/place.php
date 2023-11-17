<?php
require "config.php"; // 引入数据库配置文件

// 准备一个数组来存储所有数据
$regionsData = [];

// 获取 regions 表的数据
$regionsQuery = "SELECT region_id, name, description, image_url FROM regions";
$regionsResult = $conn->query($regionsQuery);

while ($region = $regionsResult->fetch_assoc()) {
    $regionsData[] = [
        'region_id' => $region['region_id'],
        'name' => htmlspecialchars($region['name']),
        'description' => htmlspecialchars($region['description']),
        'image_url' => htmlspecialchars($region['image_url'])
    ];
}

// 关闭数据库连接
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Region Information</title>
    <link rel="stylesheet" href="./css/place.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
    <table border="1">
        <tr>
            <th>Region ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image URL</th>
        </tr>
        <?php foreach ($regionsData as $data) : ?>
            <tr>
                <td><?= $data['region_id'] ?></td>
                <td><?= $data['name'] ?></td>
                <td><?= $data['description'] ?></td>
                <td><img src="../<?= $data['image_url'] ?>" alt="Region Image" height="100"></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        // 如果有需要添加 JavaScript 的操作，可以在这里进行
    </script>