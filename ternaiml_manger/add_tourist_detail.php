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
    // 假设你的表名为 tourist_details
    $query = "INSERT INTO tourist_details (tourist_id, title, introduction, phone, ticket, transportation, opening_hours, image1, image2, image3) 
              VALUES ('$touristId', '$title', '$introduction', '$phone', '$ticket', '$transportation', '$openingHours', NULL, NULL, NULL)";

    // 执行插入语句
    if ($conn->query($query) === TRUE) {
        $lastInsertedId = $touristId;// 获取刚插入的记录的ID

        // 处理图片上传
        for ($i = 1; $i <= 3; $i++) {
            $field = "image$i";
            if ($_FILES[$field] && isset($_FILES[$field]["size"]) && $_FILES[$field]["size"] > 0) {
                $newFileName = $touristId . '_' . basename($_FILES[$field]["name"]);
                $relativeFilePath = '../tourist_img/' . $newFileName; // 相对路径
                $targetFile = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES[$field]["tmp_name"], $targetFile)) {
                    $updateImageQuery = "UPDATE tourist_details SET $field = '$relativeFilePath' WHERE tourist_id = $lastInsertedId";
                    $conn->query($updateImageQuery);
                } else {
                    echo "Failed to upload image $field\n";
                }
            } else {
                // 如果图片没有选择，则将对应的图片字段设置为NULL
                $updateImageQuery = "UPDATE tourist_details SET $field = NULL WHERE tourist_id = $lastInsertedId";
                $conn->query($updateImageQuery);
            }
        }

        echo 'success'; // 返回成功标志给前端
    } else {
        echo 'error'; // 返回错误标志给前端
    }

    // 关闭数据库连接
    $conn->close();
} else {
    echo 'error';
}
