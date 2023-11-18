<?php
require "config.php";

if (!$conn) {
    die('数据库连接失败: ' . mysqli_connect_error());
}

// 初始化变量
$image_path = '';

// 如果有文件上传
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../home_img/";  // 保存图片的目录，确保该目录存在并具有适当的权限
    $target_file = $target_dir . basename($_FILES["image"]["name"]);  // 图片的完整路径

    // 将上传的文件移动到指定目录
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // 文件移动成功，可以将相对路径保存到数据库中
        $image_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $target_file);
    } else {
        echo "文件上传失败";
        exit; // 文件上传失败，停止执行后续代码
    }
}

// 更新数据到数据库
$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];

// 判断是否有新的图片上传
if ($image_path != '') {
    $sql = "UPDATE home SET home_name='$name', home_description='$description', home_image_url='$image_path' WHERE home_id=$id";
} else {
    // 如果没有新的图片上传，保留原先的图片路径
    $sql = "UPDATE home SET home_name='$name', home_description='$description' WHERE home_id=$id";
}

if ($conn->query($sql) === TRUE) {
    echo "数据更新成功";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// 关闭数据库连接
$conn->close();
