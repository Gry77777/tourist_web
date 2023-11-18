<?php
// Assuming you have a connection to the database and fetched data into $goodsDetailData
require "config.php";
$sql = "SELECT * FROM goods_detail";
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
    <title>商品详情表</title>
    <link rel="stylesheet" href="./css/goods_detail.css">
</head>

<body>
    <h2>特产商品管理</h2>
    <table border="1">
        <tr>
            <th>商品名称</th>
            <th>描述</th>
            <th>图片1</th>
            <th>图片2</th>
            <th>图片3</th>
            <th>附加信息</th>
            <th>相关商品1名称</th>
            <th>相关商品1描述</th>
            <th>相关商品1图片</th>
            <th>相关商品2名称</th>
            <th>相关商品2描述</th>
            <th>相关商品2图片</th>
            <th>产品规格</th>
            <th>配送信息</th>
            <th>特别优惠</th>
            <th>操作</th>

        </tr>

        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>

                <td><?= $row['product_name'] ?></td>
                <td><?= $row['description'] ?></td>
                <td><img src="../<?= $row['image1'] ?>" alt="Image 1"></td>
                <td><img src="../<?= $row['image2'] ?>" alt="Image 2"></td>
                <td><img src="../<?= $row['image3'] ?>" alt="Image 3"></td>
                <td><?= $row['additional_info'] ?></td>
                <td><?= $row['related_product1_name'] ?></td>
                <td><?= $row['related_product1_description'] ?></td>
                <td><img src="../<?= $row['related_product1_image'] ?>" alt="Related Product 1 Image"></td>
                <td><?= $row['related_product2_name'] ?></td>
                <td><?= $row['related_product2_description'] ?></td>
                <td><img src="../<?= $row['related_product2_image'] ?>" alt="Related Product 2 Image"></td>
                <td><?= $row['product_specs'] ?></td>
                <td><?= $row['shipping_info'] ?></td>
                <td><?= $row['special_offers'] ?></td>
                <td>
                    <button class="edit-btn" onclick="editRegion(this)" data-original-name="<?= $data['name'] ?>" data-original-description="<?= $data['description'] ?>" data-original-image="<?= $data['image_url'] ?>" data-region-id="<?= $data['region_id'] ?>">修改</button>
                    <button class="save-btn" style="display: none;" onclick="saveRegion(this)">保存</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                    <button class="confirm-delete-btn" style="display: none;" onclick="deleteRegion(<?= $data['region_id'] ?>)">确认删除</button>
                </td>
            </tr>
        <?php endwhile; ?>

        <tr>
            <td colspan="16">
                <button id="open-modal-btn" onclick="showAddForm()">添加</button>
            </td>
        </tr>

    </table>

    <div id="add-form-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>添加商品详情</h3>
            <form id="add-form-data" method="post" action="save_goods_detail.php" enctype="multipart/form-data">
                <label for="goods_id">商品id:</label>
                <input type="text" name="goods_id" id="goods-id"><br>

                <label for="product-name">商品名称:</label>
                <input type="text" name="product_name" id="product-name"><br>

                <label for="description">描述:</label>
                <textarea name="description" id="description"></textarea><br>

                <label for="image1">图片1:</label>
                <input type="file" name="image1" id="image1"><br>

                <label for="image2">图片2:</label>
                <input type="file" name="image2" id="image2"><br>

                <label for="image3">图片3:</label>
                <input type="file" name="image3" id="image3"><br>

                <label for="additional-info">附加信息:</label>
                <textarea name="additional_info" id="additional-info"></textarea><br>

                <label for="related-product1-name">相关商品1名称:</label>
                <input type="text" name="related_product1_name" id="related-product1-name"><br>

                <label for="related-product1-description">相关商品1描述:</label>
                <textarea name="related_product1_description" id="related-product1-description"></textarea><br>

                <label for="related-product1-image">相关商品1图片:</label>
                <input type="file" name="related_product1_image" id="related-product1-image"><br>

                <label for="related-product2-name">相关商品2名称:</label>
                <input type="text" name="related_product2_name" id="related-product2-name"><br>

                <label for="related-product2-description">相关商品2描述:</label>
                <textarea name="related_product2_description" id="related-product2-description"></textarea><br>

                <label for="related-product2-image">相关商品2图片:</label>
                <input type="file" name="related_product2_image" id="related-product2-image"><br>

                <label for="product-specs">产品规格:</label>
                <textarea name="product_specs" id="product-specs"></textarea><br>

                <label for="shipping-info">配送信息:</label>
                <textarea name="shipping_info" id="shipping-info"></textarea><br>

                <label for="special-offers">特别优惠:</label>
                <textarea name="special_offers" id="special-offers"></textarea><br>
                <button type="submit" onclick="addGoodsDetail(event)">保存</button>
            </form>
        </div>
    </div>

    <script src="./js/jquery3.6.3.js"></script>
    <script>
        function showAddForm() {
            $('#add-form-modal').show();
        }

        function closeModal() {
            $('#add-form-modal').hide();
        }

        function addGoodsDetail(event) {
            event.preventDefault();

            closeModal();
        }

        function addGoodsDetail(event) {
            event.preventDefault();
            var formData = new FormData($('#add-form-data')[0]); // 使用FormData对象来获取表单数据，包括文件数据
            $.ajax({
                url: 'save_goods_detail.php',
                type: 'POST',
                data: formData,
                processData: false, // 不对数据进行序列化处理
                contentType: false, // 不设置内容类型
                success: function(response) {
                    // 处理成功响应
                    console.log(response);
                    // 刷新页面或者使用新数据更新表格
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // 处理错误
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

</body>

</html>

<?php
// Close the connection
$conn->close();
?>