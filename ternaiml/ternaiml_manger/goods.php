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
    <link rel="stylesheet" href="./css/place.css">
    <style>
        /* ... 省略前面的样式代码 ... */

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            font-size: 14px;
            text-decoration: none;
            background-color: #f2f2f2;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active {
            background-color: #008CBA;
            color: white;
        }
    </style>
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
            <table>
                <tr>
                    <th>商品ID</th>
                    <th>名称</th>
                    <th>简介</th>
                    <th>图片</th>
                </tr>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['goods_id'] . '</td>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['description'] . '</td>';
                    echo '<td><img src="../' . $row['image'] . '" alt="goods Image"></td>';
                    
                    echo '</tr>';
                }
                ?>
            </table>
        </div>

        <div class="pagination">
            <?php
            for ($i = 1; $i <= $totalPages; $i++) {
                $activeClass = ($i == $page) ? 'class="active"' : '';
                echo '<a href="?page=' . $i . '" ' . $activeClass . '>' . $i . '</a>';
            }
            ?>
        </div>

    </div>
</body>

</html>