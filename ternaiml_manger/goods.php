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
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            z-index: 1000;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>

<body>
    <h2>
        特产商品
    </h2>
    <table>
        <tr>
            <th>商品ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>图片</th>
            <th>操作</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['goods_id'] . '</td>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['description'] . '</td>';
            echo '<td><img src="../' . $row['image'] . '" alt="goods Image"></td>';
            echo '<td>';
            echo '<button class="modify-btn" data-id="' . $row['goods_id'] . '">修改</button>';
            echo '<button class="delete-btn" data-id="' . $row['goods_id'] . '">删除</button>';
            echo '</td>';
            echo '</tr>';
        }
        '</tr>';
        ?>
        <tr>
            <td colspan="5">
                <button class="add-btn">添加商品</button>
            </td>
        </tr>

        <div class="overlay" id="overlay"></div>
        <div class="modal" id="modal">
            <h2>添加商品</h2>
            <form id="addForm">
                <label for="name">商品名称:</label>
                <input type="text" name="name" required>
                <br>
                <label for="description">商品简介:</label>
                <textarea name="description" required></textarea>
                <br>
                <!-- 其他商品信息的输入字段，根据你的数据库表结构 -->
                <br>
                <input type="submit" value="添加">
                <button type="button" id="cancelBtn">取消</button>
            </form>
        </div>

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
</body>

<script>
    $(function() {
        $(".add-btn").on("click", function() {
            $("#overlay, #modal").show();
        });

        // 关闭模态框
        $("#cancelBtn").on("click", function() {
            $("#overlay, #modal").hide();
        });

        // 处理表单提交
        $("#addForm").on("submit", function(event) {
            event.preventDefault();

            // 处理表单提交逻辑，可以使用 AJAX 发送数据到服务器
            // 然后在服务器端进行插入操作，类似前面提到的 PHP 代码
            // 注意：这里只是一个示例，实际上需要添加更多的验证和安全性检查
            alert("商品添加成功！");

            // 关闭模态框
            $("#overlay, #modal").hide();
        });
    });
</script>

</html>