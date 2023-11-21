<?php
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $touristId = $_POST['tourist_id'];
    $title = $_POST['title'];
    $introduction = $_POST['introduction'];
    $phone = $_POST['phone'];
    $ticket = $_POST['ticket'];
    $transportation = $_POST['transportation'];
    $openingHours = $_POST['opening_hours'];

    // 设置目标文件夹的权限为可写
    $targetDir = '../tourist_img/';
    chmod($targetDir, 0777);

    // 假设你的表名为 tourist_details
    $query = "INSERT INTO tourist_details (tourist_id, title, introduction, phone, ticket, transportation, opening_hours) 
              VALUES ('$touristId', '$title', '$introduction', '$phone', '$ticket', '$transportation', '$openingHours')";

    // 执行插入语句
    if ($conn->query($query) === TRUE) {
        $lastInsertedId = $conn->insert_id; // 获取刚插入的记录的ID

        // 处理图片上传
        $image1 = handleImageUpload('image1', $lastInsertedId, '../tourist_img/');
        $image2 = handleImageUpload('image2', $lastInsertedId, '../tourist_img/');
        $image3 = handleImageUpload('image3', $lastInsertedId, '../tourist_img/');

        // 更新记录中的图片路径
        $updateImageQuery = "UPDATE tourist_details 
                             SET image1 = '$image1', image2 = '$image2', image3 = '$image3' 
                             WHERE tourist_id = $lastInsertedId";

        // 执行更新图片路径的语句
        $conn->query($updateImageQuery);

        echo 'success'; // 返回成功标志给前端
    } else {
        echo 'error'; // 返回错误标志给前端
    }

    // 关闭数据库连接
    $conn->close();
} else {
    echo 'error';
}

function handleImageUpload($inputName, $touristId, $targetDir)
{
    // 检查是否有指定名称的文件上传输入框
    if (!isset($_FILES[$inputName])) {
        return ""; // 返回空字符串表示上传失败
    }

    // 获取上传的文件信息
    $file = $_FILES[$inputName];

    // 检查上传的文件是否为空
    if ($file['error'] == UPLOAD_ERR_NO_FILE) {
        return ""; // 返回空字符串表示上传失败
    }

    // 检查上传的文件是否是一个有效的文件
    if (!is_uploaded_file($file['tmp_name'])) {
        return ""; // 返回空字符串表示上传失败
    }

    // 生成新的文件名
    $newFileName = $touristId . '_' . basename($file["name"]);

    // 将图片从临时目录移动到指定目录，并返回文件名
    $targetFile = $targetDir . $newFileName;
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        // 返回文件名
        return $newFileName;
    } else {
        return ""; // 返回空字符串表示上传失败
    }
}
