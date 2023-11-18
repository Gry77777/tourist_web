<?php
require "config.php";

// 设置每页显示的记录数
$recordsPerPage = 5;
// 获取当前页数，默认为第一页
$page = isset($_GET['page']) ? $_GET['page'] : 1;
// 计算偏移量
$offset = ($page - 1) * $recordsPerPage;
// 查询数据库获取当前页的数据
$query = "SELECT * FROM home LIMIT $offset, $recordsPerPage";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('数据库查询失败: ' . mysqli_error($conn));
}
// 查询总记录数
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM home";
$totalResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalResult)['total'];

// 计算总页数
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站首页</title>
    <link rel="stylesheet" href="./css/home.css">
</head>

<body>
    <div class="container">
        <div class="top">
            <img src="./image/home.png" alt="">
            <h2>网站首页</h2>
        </div>

        <div class="main">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<a href="detail.php?homeid=' . $row['home_id'] . '">';
                echo '<div class="list">';
                echo '<div class="item">';
                echo '<div class="left">';
                echo '<img src="' . $row['home_image_url'] . '" alt="Image">';
                echo '</div>';
                echo '<div class="right">';
                echo '<h3>' . htmlspecialchars($row['home_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($row['home_description']) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
            }
            ?>
        </div>

        <div class="pagination">
            <?php
            // 显示上一页按钮
            if ($page > 1) {
                echo '<a href="?page=' . ($page - 1) . '">上一页</a>';
            }
            // 显示翻页按钮
            for ($i = 1; $i <= $totalPages; $i++) {
                echo '<a href="?page=' . $i . '">' . $i . '</a>';
            }
            // 显示下一页按钮
            if ($page < $totalPages) {
                echo '<a href="?page=' . ($page + 1) . '">下一页</a>';
            }
            ?>
        </div>

    </div>
</body>

</html>