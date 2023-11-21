<?php
require "config.php";

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 获取其他表单数据
$goodsId = $_POST['goods_id'];
$updatedName = $_POST['product_name'];
$updatedDescription = $_POST['description'];
$additionalInfo = $_POST['additional_info'];
$relatedProduct1Name = $_POST['related_product1_name'];
$relatedProduct1Description = $_POST['related_product1_description'];
$relatedProduct2Name = $_POST['related_product2_name'];
$relatedProduct2Description = $_POST['related_product2_description'];
$productSpecs = $_POST['product_specs'];
$shippingInfo = $_POST['shipping_info'];
$specialOffers = $_POST['special_offers'];

echo "Debug: Received data - ";
var_dump($_POST);
// 更新商品信息
$sqlUpdate = "UPDATE goods_detail SET 
    product_name='$updatedName', 
    description='$updatedDescription',
    additional_info='$additionalInfo',
    related_product1_name='$relatedProduct1Name',
    related_product1_description='$relatedProduct1Description',
    related_product2_name='$relatedProduct2Name',
    related_product2_description='$relatedProduct2Description',
    product_specs='$productSpecs',
    shipping_info='$shippingInfo',
    special_offers='$specialOffers'
    WHERE goods_id=$goodsId";

// 输出 SQL 语句进行调试
echo "Debug SQL: $sqlUpdate <br>";

if ($conn->query($sqlUpdate) === TRUE) {
    echo "商品信息更新成功<br>";
} else {
    echo "Error updating goods detail: " . $conn->error . "<br>";
}

// 处理图片
function handleImage($file, $fieldName, $goodsId, $conn)
{
    // 检查索引是否设置
    if (isset($file['name'])) {
        if (!empty($file['name'])) {
            $targetDir = "../goods_img/";
            $targetFile = $targetDir . basename($file['name']);

            move_uploaded_file($file['tmp_name'], $targetFile);

            $sqlImageUpdate = "UPDATE goods_detail SET $fieldName='$targetFile' WHERE goods_id=$goodsId";

            // 输出 SQL 语句进行调试
            echo "Debug SQL: $sqlImageUpdate <br>";

            if ($conn->query($sqlImageUpdate) !== TRUE) {
                echo "Error updating image: " . $conn->error . "<br>";
            } else {
                echo "图片更新成功<br>";
            }
        }
    } else {
        echo "未上传 $fieldName 图片<br>";
    }
}


// 处理所有图片字段
handleImage($_FILES['image1'], 'image1', $goodsId, $conn);
handleImage($_FILES['image2'], 'image2', $goodsId, $conn);
handleImage($_FILES['image3'], 'image3', $goodsId, $conn);
handleImage($_FILES['related_product1_image'], 'related_product1_image', $goodsId, $conn);
handleImage($_FILES['related_product2_image'], 'related_product2_image', $goodsId, $conn);

// 关闭数据库连接
$conn->close();
