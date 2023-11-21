<?php
require "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 连接数据库
    // 获取POST请求中的数据
    $goodsId = $_POST['goodsId'];
    $newName = $_POST['newName'];
    $newDescription = $_POST['newDescription'];

    // 如果有上传的新图片
    if ($_FILES['newImage']['name']) {
        $targetDir =  "../goods_img/";
        $targetFile = $targetDir . basename($_FILES['newImage']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 检查文件是否为图像
        $check = getimagesize($_FILES['newImage']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $response = array(
                'status' => 'error',
                'message' => '文件不是图像'
            );
            echo json_encode($response);
            exit;
        }

        // 检查文件是否已存在
        if (file_exists($targetFile)) {
            $response = array(
                'status' => 'error',
                'message' => '文件已存在'
            );
            echo json_encode($response);
            exit;
        }

        // 检查文件大小
        if ($_FILES['newImage']['size'] > 500000) {
            $response = array(
                'status' => 'error',
                'message' => '文件过大'
            );
            echo json_encode($response);
            exit;
        }

        // 允许的文件格式
        $allowedFormats = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $allowedFormats)) {
            $response = array(
                'status' => 'error',
                'message' => '仅支持 JPG, JPEG, PNG 和 GIF 格式的文件'
            );
            echo json_encode($response);
            exit;
        }

        // 如果通过了所有检查，尝试移动文件
        if (move_uploaded_file($_FILES['newImage']['tmp_name'], $targetFile)) {
            // 更新数据库中的商品信息，包括新图片路径
            $sql = "UPDATE goods SET name = '$newName', description = '$newDescription', image = '$targetFile' WHERE goods_id = $goodsId";

            if ($conn->query($sql) === TRUE) {
                $response = array(
                    'status' => 'success',
                    'message' => '商品修改成功'
                );
                echo json_encode($response);
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => '商品修改失败: ' . $conn->error
                );
                echo json_encode($response);
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => '文件上传失败'
            );
            echo json_encode($response);
        }
    } else {
        // 如果没有上传新图片，只更新商品信息
        $sql = "UPDATE goods SET name = '$newName', description = '$newDescription' WHERE goods_id = $goodsId";

        if ($conn->query($sql) === TRUE) {
            $response = array(
                'status' => 'success',
                'message' => '商品修改成功'
            );
            echo json_encode($response);
        } else {
            $response = array(
                'status' => 'error',
                'message' => '商品修改失败: ' . $conn->error
            );
            echo json_encode($response);
        }
    }

    // 关闭数据库连接
    $conn->close();
} else {
    // 如果不是POST请求，返回错误信息
    $response = array(
        'status' => 'error',
        'message' => '无效的请求方法'
    );
    echo json_encode($response);
}
