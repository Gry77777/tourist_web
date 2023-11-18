<?php
require "config.php";

$itemsPerPage = 9;

// 获取当前页码，如果没有指定，默认为第一页
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// 计算起始行和结束行
$start = ($page - 1) * $itemsPerPage;
$sql = "SELECT * FROM goods LIMIT $start, $itemsPerPage";
$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

// 计算总页数
$totalItemsQuery = "SELECT COUNT(*) AS total FROM goods";
$totalItemsResult = $conn->query($totalItemsQuery);
$totalItems = $totalItemsResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站首页</title>
    <link rel="stylesheet" href="./css/goods.css">
</head>




<body>
    <div class="container">
        <div class="top">
            <img src="./image/shopping.png" alt="">
            <h2>
                特产商品
            </h2>
        </div>
        <div class="main">
            <ul class="product-list">
                <?php
                // 遍历查询结果，生成商品内容
                while ($row = $result->fetch_assoc()) {
                    echo '<li class="product" data-product-id="' . $row['goods_id'] . '">';
                    echo '<a href="goods_detail.php?goods_id=' . $row['goods_id'] . '" target="_blank">'; // 设置跳转链接
                    echo '<div class="product-image">';
                    // 如果有图片字段，你也可以在这里输出图片
                    if (!empty($row['image'])) {
                        // 使用相对路径显示图片
                        echo '<img src="../' . $row['image'] . '" alt="goods Image">';
                    }
                    echo '</div>';
                    echo '<div class="product-info">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p class="description">' . $row['description'] . '</p>';
                    echo '</div>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>

        <div class="pagination">
            <?php
            // 显示翻页按钮
            for ($i = 1; $i <= $totalPages; $i++) {
                $activeClass = ($i == $page) ? 'class="active"' : '';
                echo '<a href="?page=' . $i . '" ' . $activeClass . '>' . $i . '</a>';
            }
            ?>
        </div>
    </div>
</body>

</html>