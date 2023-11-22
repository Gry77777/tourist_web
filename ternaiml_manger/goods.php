<?php
require "config.php";

$itemsPerPage = 9;

// 获取当前页码，如果没有指定，默认为第一页
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// 获取搜索关键字，如果没有指定，默认为空
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// 计算起始行和结束行
$start = ($page - 1) * $itemsPerPage;

// 通过添加条件判断是否存在搜索关键字
$searchCondition = !empty($searchKeyword) ? "AND (name LIKE '%$searchKeyword%' OR description LIKE '%$searchKeyword%')" : '';

$sql = "SELECT * FROM goods WHERE 1 $searchCondition LIMIT $start, $itemsPerPage";
$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

// 计算总页数
$totalItemsQuery = "SELECT COUNT(*) AS total FROM goods WHERE 1 $searchCondition";
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
    <script src="./js/jquery3.6.3.js"></script>
    <style>
        /* 拟态框样式 */
    </style>
</head>

<body>
    <div class="overlay" id="overlay"></div>
    <!-- 修改商品的拟态框 -->
    <div class="modal" id="modifyModal">
        <h2>修改商品</h2>
        <form id="modifyForm" enctype="multipart/form-data">
            <input type="hidden" id="modifyGoodsId" name="goodsId">
            <label for="modifyName">商品名称:</label>
            <input type="text" id="modifyName" name="name" required>
            <br>
            <label for="modifyDescription">商品简介:</label>
            <textarea id="modifyDescription" name="description" required></textarea>
            <br>
            <label for="modifyImage">商品图片:</label>
            <input type="file" id="modifyImage" name="image">
            <br>
            <br>
            <input type="submit" value="保存修改">
            <button type="button" class="cancelBtn" data-modal="modifyModal">取消</button>
        </form>
    </div>


    <!-- 添加商品的拟态框 -->
    <div class="modal" id="addModal">
        <h2>添加商品</h2>
        <form id="addForm">
            <!-- 商品信息的输入字段，根据你的数据库表结构 -->
            <label for="addName">商品名称:</label>
            <input type="text" id="addName" name="name" required>
            <br>
            <label for="addDescription">商品简介:</label>
            <textarea id="addDescription" name="description" required></textarea>
            <br>

            <label for="addImage">商品图片:</label>
            <input type="file" id="addImage" name="image">
            <!-- 其他商品信息的输入字段，根据你的数据库表结构 -->
            <br>
            <input type="submit" value="添加商品">
            <button type="button" class="cancelBtn" data-modal="addModal">取消</button>
        </form>
    </div>

    <h2>
        特产商品
    </h2>
    <div class="search">
        <form method="GET" action="">
            <label for="search">搜索商品:</label>
            <input type="text" id="search" name="search">
            <input type="submit" value="搜索">
        </form>
    </div>
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
            echo '<td class="name">' . $row['name'] . '</td>';
            echo '<td class="description">' . $row['description'] . '</td>';
            echo '<td><img src="../' . $row['image'] . '" alt="goods Image"></td>';
            echo '<td>';
             echo '<button class="modify-btn" data-id="' . $row['goods_id'] . '" data-name="' . $row['name'] . '" data-description="' . $row['description'] . '" data-image="' . $row['image'] . '">修改</button>';
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
    $(document).ready(function() {
        $('.delete-btn').on('click', function() {
            // 获取商品ID
            var goodsId = $(this).data('id');
            // 弹出确认对话框
            var confirmDelete = confirm("确认删除该商品吗？");
            // 如果用户确认删除
            if (confirmDelete) {
                // 发送Ajax请求
                $.ajax({
                    type: 'POST', // 或者使用 'GET'，取决于你的删除操作
                    url: 'goods_delete.php', // 替换为实际处理删除请求的文件路径
                    data: {
                        goodsId: goodsId
                    },
                    success: function(response) {
                        // 在删除成功后执行的操作，可以根据需要刷新商品列表或其他操作
                        console.log(response);
                        location.reload();
                        // 这里你可以添加代码来更新商品列表和分页信息
                    },
                    error: function(error) {
                        console.error('删除请求失败：', error);
                    }
                });
            }
        });

        //修改的拟态框js

        $('.modify-btn').on('click', function() {
            // 获取商品ID
            var goodsId = $(this).data('id');
            // 在这里可以根据商品ID发送请求获取商品信息，然后将信息填充到表单中
            // 这里仅是示例，实际应用中需要根据你的数据结构进行修改
            var name = $(this).closest('tr').find('.name').text();
            var description = $(this).closest('tr').find('.description').text();

            // 填充表单
            $('#modifyGoodsId').val(goodsId);
            $('#modifyName').val(name);
            $('#modifyDescription').html(description);
            // 显示拟态框
            $('#overlay, #modifyModal').show();
        });

        // 模拟点击添加按钮，弹出添加拟态框
        $('.add-btn').on('click', function() {
            // 在这里可以清空表单或进行其他初始化操作

            // 显示拟态框
            $('#overlay, #addModal').show();
        });

        // 关闭拟态框
        $('.cancelBtn').on('click', function() {
            var modalId = $(this).data('modal');
            $('#' + modalId + ', #overlay').hide();
        });

        // 处理修改表单提交
        // 处理修改表单提交
        $('#modifyForm').on('submit', function(event) {
            event.preventDefault();
            // 在这里可以获取修改表单数据，然后发送Ajax请求进行更新操作
            var goodsId = $('#modifyGoodsId').val();
            var newName = $('#modifyName').val();
            var newDescription = $('#modifyDescription').val();
            var newImage = $('#modifyImage')[0].files[0]; // 获取选择的文件
            // 如果选择了新图片
            if (newImage) {
                // 创建 FormData 对象，用于发送包含文件的表单数据
                var formData = new FormData();
                formData.append('goodsId', goodsId);
                formData.append('newName', newName);
                formData.append('newDescription', newDescription);
                formData.append('newImage', newImage);

                // 发送Ajax请求
                $.ajax({
                    type: 'POST',
                    url: 'goods_modify.php', // 替换为实际处理修改请求的文件路径
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // 在修改成功后执行的操作，可以根据需要刷新商品列表或其他操作
                        console.log(response);
                        location.reload();
                        // 这里你可以添加代码来更新商品列表和分页信息
                    },
                    error: function(error) {
                        console.error('修改请求失败：', error);
                    }
                });
            } else {
                // 如果没有选择新图片，直接发送请求，不包含图片数据
                $.ajax({
                    type: 'POST',
                    url: 'goods_modify.php', // 替换为实际处理修改请求的文件路径
                    data: {
                        goodsId: goodsId,
                        newName: newName,
                        newDescription: newDescription
                    },
                    success: function(response) {
                        // 在修改成功后执行的操作，可以根据需要刷新商品列表或其他操作
                        console.log(response);
                        location.reload();
                        // 这里你可以添加代码来更新商品列表和分页信息
                    },
                    error: function(error) {
                        console.error('修改请求失败：', error);
                    }
                });
            }
        });;

        $('#addForm').on('submit', function(event) {
            event.preventDefault();

            // 获取添加表单数据
            var newName = $('#addName').val();
            var newDescription = $('#addDescription').val();
            var newImage = $('#addImage')[0].files[0]; // 获取选择的文件

            // 创建 FormData 对象，用于发送包含文件的表单数据
            var formData = new FormData();
            formData.append('newName', newName);
            formData.append('newDescription', newDescription);

            // 如果选择了新图片，才添加到 FormData
            if (newImage) {
                formData.append('newImage', newImage);
            }

            // 发送Ajax请求
            $.ajax({
                type: 'POST',
                url: 'goods_add.php', // 替换为实际处理添加请求的文件路径
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // 在添加成功后执行的操作，可以根据需要刷新商品列表或其他操作
                    console.log(response);
                    location.reload();
                    // 这里你可以添加代码来更新商品列表和分页信息
                },
                error: function(error) {
                    console.error('添加请求失败：', error);
                }
            });
        });

    });
</script>

</html>