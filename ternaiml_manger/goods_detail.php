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
            <th>id</th>
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
                <td><?= $row['goods_id'] ?></td>
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
                    <button class="edit-btn" onclick="editGoodsDetail(this)" data-original-name="<?= $row['product_name'] ?>" data-original-description="<?= $row['description'] ?>" data-original-image1="<?= $row['image1'] ?>" data-original-image2="<?= $row['image2'] ?>" data-original-image3="<?= $row['image3'] ?>" data-original-additional-info="<?= $row['additional_info'] ?>" data-original-related-product1-name="<?= $row['related_product1_name'] ?>" data-original-related-product1-description="<?= $row['related_product1_description'] ?>" data-original-related-product1-image="<?= $row['related_product1_image'] ?>" data-original-related-product2-name="<?= $row['related_product2_name'] ?>" data-original-related-product2-description="<?= $row['related_product2_description'] ?>" data-original-related-product2-image="<?= $row['related_product2_image'] ?>" data-original-product-specs="<?= $row['product_specs'] ?>" data-original-shipping-info="<?= $row['shipping_info'] ?>" data-original-special-offers="<?= $row['special_offers'] ?>" data-region-id="<?= $row['goods_id'] ?>">修改</button>
                    <button class="delete-btn" data-goods-id="<?= $row['goods_id'] ?>" onclick="confirmDelete(this)">删除</button>
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

    <!-- 添加一个模态框 -->
    <div id="edit-form-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>修改商品详情</h3>
            <!-- 编辑表单 -->
            <form id="edit-form-data" method="post" action="update_goods_detail.php" enctype="multipart/form-data">
                <!-- 商品ID -->
                <label for="goods-id-edit">商品ID:</label>
                <input type="text" name="goods_id" id="goods-id-edit"><br>
                a
                <!-- 商品名称 -->
                <label for="product-name-edit">商品名称:</label>
                <input type="text" name="product_name" id="product-name-edit"><br>

                <!-- 描述 -->
                <label for="description-edit">描述:</label>
                <textarea name="description" id="description-edit"></textarea><br>

                <!-- 图片1 -->
                <label for="image1-edit">图片1:</label>
                <input type="file" name="image1" id="image1-edit"><br>

                <!-- 图片2 -->
                <label for="image2-edit">图片2:</label>
                <input type="file" name="image2" id="image2-edit"><br>

                <!-- 图片3 -->
                <label for="image3-edit">图片3:</label>
                <input type="file" name="image3" id="image3-edit"><br>

                <!-- 附加信息 -->
                <label for="additional-info-edit">附加信息:</label>
                <textarea name="additional_info" id="additional-info-edit"></textarea><br>

                <!-- 相关商品1名称 -->
                <label for="related-product1-name-edit">相关商品1名称:</label>
                <input type="text" name="related_product1_name" id="related-product1-name-edit"><br>

                <!-- 相关商品1描述 -->
                <label for="related-product1-description-edit">相关商品1描述:</label>
                <textarea name="related_product1_description" id="related-product1-description-edit"></textarea><br>

                <!-- 相关商品1图片 -->
                <label for="related-product1-image-edit">相关商品1图片:</label>
                <input type="file" name="related_product1_image" id="related-product1-image-edit"><br>

                <!-- 相关商品2名称 -->
                <label for="related-product2-name-edit">相关商品2名称:</label>
                <input type="text" name="related_product2_name" id="related-product2-name-edit"><br>

                <!-- 相关商品2描述 -->
                <label for="related-product2-description-edit">相关商品2描述:</label>
                <textarea name="related_product2_description" id="related-product2-description-edit"></textarea><br>

                <!-- 相关商品2图片 -->
                <label for="related-product2-image-edit">相关商品2图片:</label>
                <input type="file" name="related_product2_image" id="related-product2-image-edit"><br>

                <!-- 产品规格 -->
                <label for="product-specs-edit">产品规格:</label>
                <textarea name="product_specs" id="product-specs-edit"></textarea><br>

                <!-- 配送信息 -->
                <label for="shipping-info-edit">配送信息:</label>
                <textarea name="shipping_info" id="shipping-info-edit"></textarea><br>

                <!-- 特别优惠 -->
                <label for="special-offers-edit">特别优惠:</label>
                <textarea name="special_offers" id="special-offers-edit"></textarea><br>

                <button type="submit" onclick="updateGoodsDetail(event)">保存修改</button>
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
            //删除按钮的js代码
        };

        function confirmDelete(button) {
            // 使用 confirm 函数显示一个确认框
            var result = confirm("确认删除该商品？");
            // 如果用户点击确认按钮
            if (result) {
                // 获取商品 ID
                var goodsid = $(button).data('goods-id');
                // 发送 AJAX 请求删除商品
                $.ajax({
                    type: 'POST',
                    url: 'delete_goods_detail.php',
                    data: {
                        goodsid: goodsid
                    },
                    success: function(response) {
                        // 处理删除成功响应
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
        }

        //修改的ajax代码
        function closeEditModal() {
            $('#edit-form-modal').hide();
        }

        function editGoodsDetail(button) {
            $('#edit-form-modal').show();
            var goods_id = $(button).data('goods-id');
            var originalName = $(button).data('original-name');
            var originalDescription = $(button).data('original-description');
            var originalAdditionalInfo = $(button).data('original-additional-info');
            var originalRelatedProduct1Name = $(button).data('original-related-product1-name');
            var originalRelatedProduct1Name = $(button).data('original-related-product1-name');
            var originalRelatedProduct1Description = $(button).data('original-related-product1-description');
            var originalRelatedProduct2Name = $(button).data('original-related-product2-name');
            var originalRelatedProduct2Description = $(button).data('original-related-product2-description');
            var originalProductSpecs = $(button).data('original-product-specs');
            var originalShippingInfo = $(button).data('original-shipping-info');
            var originalSpecialOffers = $(button).data('original-special-offers');
            var regionId = $(button).data('region-id');

            // 将原始数据填充到编辑模态框中
            $('#goods-id-edit').val(regionId);
            $('#product-name-edit').val(originalName);
            $('#description-edit').text(originalDescription);
            $('#additional-info-edit').val(originalAdditionalInfo);
            $('#related-product1-name-edit').val(originalRelatedProduct1Name);

            $('#related-product1-description-edit').text(originalRelatedProduct1Description);
            $('#related-product2-name-edit').val(originalRelatedProduct2Name);
            $('#related-product2-description-edit').text(originalRelatedProduct2Description);
            $('#product-specs-edit').text(originalProductSpecs);
            $('#shipping-info-edit').text(originalShippingInfo);
            $('#special-offers-edit').text(originalSpecialOffers);
            $('#goods-id-edit').text(regionId);

            // 显示编辑模态框
        }

        function updateGoodsDetail(event) {
            event.preventDefault();

            // 创建 FormData 对象
            var formData = new FormData();

            // 逐个添加表单字段
            formData.append('goods_id', $('#goods-id-edit').val());
            formData.append('product_name', $('#product-name-edit').val());
            formData.append('description', $('#description-edit').val());
            formData.append('image1', $('#image1-edit')[0].files[0]);
            formData.append('image2', $('#image2-edit')[0].files[0]);
            formData.append('image3', $('#image3-edit')[0].files[0]);
            formData.append('additional_info', $('#additional-info-edit').val());
            formData.append('related_product1_name', $('#related-product1-name-edit').val());
            formData.append('related_product1_description', $('#related-product1-description-edit').val());
            formData.append('related_product1_image', $('#related-product1-image-edit')[0].files[0]);
            formData.append('related_product2_name', $('#related-product2-name-edit').val());
            formData.append('related_product2_description', $('#related-product2-description-edit').val());
            formData.append('related_product2_image', $('#related-product2-image-edit')[0].files[0]);
            formData.append('product_specs', $('#product-specs-edit').val());
            formData.append('shipping_info', $('#shipping-info-edit').val());
            formData.append('special_offers', $('#special-offers-edit').val());

            formData.forEach(function(value, key) {
                console.log(key, value);
            });
            // 发送 AJAX 请求更新数据
            $.ajax({
                url: 'update_goods_detail.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
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

            // 关闭编辑模态框
            closeEditModal();
        }
    </script>

</body>

</html>

<?php
// Close the connection
$conn->close();
?>