<?php
require "config.php";
$result = mysqli_query($conn, "SELECT * FROM tourists_place");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>金华旅游景区</title>
    <link rel="stylesheet" href="./css/tourists.css">
</head>

<body>
    <div class="container">
        <div class="top">
            <img src="./image/navigate.png" alt="">
            <h2>
                金华旅游景区
            </h2>
        </div>
        <div class="main">
            <ul class="tourist-list">
                <?php
                $items_per_row = 5; // 每行显示的景区数量
                $rows_per_page = 3; // 每页显示的行数
                $total_items = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tourists_place"));
                $total_pages = ceil($total_items / ($items_per_row * $rows_per_page));
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1; // 当前页码，默认为第一页
                $offset = ($current_page - 1) * $items_per_row * $rows_per_page;
                // 从数据库中获取指定范围的景区数据
                $query = "SELECT * FROM tourists_place LIMIT $offset, " . ($items_per_row * $rows_per_page);
                $result = mysqli_query($conn, $query);
                // 计算当前行数
                $current_row = 0;

                // 遍历查询结果，将每个景区信息输出为一个 <li> 元素
                while ($row = mysqli_fetch_assoc($result)) {
                    $tourist_id = $row['tourist_id'];
                    $name = $row['name'];
                    $img = $row['img'];

                    // 输出每个景区信息，并将 tourist_id 添加到 <a> 的 id 属性中
                    echo "<li>";
                    echo "<a href='tourist_detail.php?tourist_id=$tourist_id' id='tourist_$tourist_id' target='_blank'>";
                    echo "<img src='$img' alt=''>";
                    echo "<h3>$name</h3>";
                    echo "</a>";
                    echo "</li>";

                    // 如果已经显示了每行指定数量的景区，增加当前行数
                    if (($current_row + 1) % $items_per_row === 0) {
                        $current_row++;
                    }
                }
                ?>
            </ul>

            <!-- 输入页码搜索框 -->
            <div class="pagination-container">
                <!-- 翻页按钮 -->
                <div class="pagination">
                    <?php
                    if ($current_page > 1) {
                        echo "<a href='?page=1' class='page-btn'>&laquo;&laquo;</a>"; // 首页
                        echo "<a href='?page=" . ($current_page - 1) . "' class='page-btn'>&laquo; 上一页</a>"; // 上一页
                    }

                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active_class = ($i == $current_page) ? 'active' : '';
                        echo "<a href='?page=$i' class='page-btn $active_class'>$i</a>";
                    }

                    if ($current_page < $total_pages) {
                        echo "<a href='?page=" . ($current_page + 1) . "' class='page-btn'>下一页 &raquo;</a>"; // 下一页
                        echo "<a href='?page=$total_pages' class='page-btn'>&raquo;&raquo;</a>"; // 尾页
                    }
                    ?>
                </div>
                <div class="search">
                    <form action="" method="get">
                        <input type="number" name="page" placeholder="输入页码" min="1" max="<?php echo $total_pages; ?>">
                        <button type="submit">跳转</button>
                    </form>
                </div>
            </div>

        </div>





    </div>


</body>

</html>