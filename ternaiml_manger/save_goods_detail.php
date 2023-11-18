<?php
// Assuming you have a connection to the database
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $goodsId = $_POST['goods_id'];
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $image1Path = uploadImage($_FILES['image1'], '../goods_img');
    $image2Path = uploadImage($_FILES['image2'], '../goods_img');
    $image3Path = uploadImage($_FILES['image3'], '../goods_img');
    $additionalInfo = $_POST['additional_info'];
    $relatedProduct1Name = $_POST['related_product1_name'];
    $relatedProduct1Description = $_POST['related_product1_description'];
    $relatedProduct1Image = uploadImage($_FILES['related_product1_image'], '../goods_img');
    $relatedProduct2Name = $_POST['related_product2_name'];
    $relatedProduct2Description = $_POST['related_product2_description'];
    $relatedProduct2Image = uploadImage($_FILES['related_product2_image'], '../goods_img');
    $productSpecs = $_POST['product_specs'];
    $shippingInfo = $_POST['shipping_info'];
    $specialOffers = $_POST['special_offers'];
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO goods_detail (goods_id, product_name, description, image1, image2, image3, additional_info, related_product1_name, related_product1_description, related_product1_image, related_product2_name, related_product2_description, related_product2_image, product_specs, shipping_info, special_offers) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssss", $goodsId, $productName, $description, $image1Path, $image2Path, $image3Path, $additionalInfo, $relatedProduct1Name, $relatedProduct1Description, $relatedProduct1Image, $relatedProduct2Name, $relatedProduct2Description, $relatedProduct2Image, $productSpecs, $shippingInfo, $specialOffers);

    if ($stmt->execute()) {
        echo "保存成功";
    } else {
        echo "保存失败：" . mysqli_error($conn);
    }
    // Close the statement
    $stmt->close();
}

function uploadImage($file, $targetDirectory)
{
    if ($file['error'] === UPLOAD_ERR_OK) {
        // 检查文件类型
        $allowedTypes = array('image/jpeg', 'image/png');
        if (!in_array($file['type'], $allowedTypes)) {
            return null; // 文件类型不符合要求
        }
        $targetFile = $targetDirectory . '/' . uniqid() . '_' . basename($file['name']);
        // 确保目标文件夹存在并具有适当的权限
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
        // 移动文件并返回相对路径
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        } else {
            // 文件移动失败
            return null;
        }
    } else {
        // 文件上传出错
        return null;
    }
}


$conn->close();
