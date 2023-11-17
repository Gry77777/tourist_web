<?php
require "config.php";
$sql = "SELECT * FROM regions";
$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站首页</title>
    <link rel="stylesheet" href="./css/place.css">
</head>

<body>
    <div class="container">
        <div class="top">
            <img src="./image/flight.png" alt="">
            <h2>
                金华各地
            </h2>
        </div>
        <div class="main">
            <ul>
                <?php
                // 遍历查询结果，生成地区内容
                while ($row = $result->fetch_assoc()) {
                    echo '<li class="region" data-region-id="' . $row['region_id'] . '">';
                    echo '<a href="place_detail copy.php?region_id=' . $row['region_id'] . '" target="_blank">'; // 设置跳转链接
                    echo '<h3>';
                    echo $row['name'];
                    // 如果有图片字段，你也可以在这里输出图片
                    if (!empty($row['image_url'])) {
                        // 使用相对路径显示图片
                        echo '<img src="' . $row['image_url'] . '" alt="Region Image">';
                    }
                    echo '</h3>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>


    </div>
</body>

</html>