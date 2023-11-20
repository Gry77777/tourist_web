<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取POST请求中的数据
    $id = isset($_POST['id']) ? $_POST['id'] : null; // 允许用户输入id
    $name = $_POST['name'];
    $description = $_POST['description'];

    // 处理上传的图片文件
    $uploadDir = "../home_img/"; // 请确保该目录存在，并设置正确的权限
    $imageFileName = $uploadDir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $imageFileName);

    // 执行插入操作
    $query = "INSERT INTO home (home_id, home_name, home_description, home_image_url) VALUES ('$id', '$name', '$description', '$imageFileName')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('添加信息失败: ' . mysqli_error($conn));
    } else {
        echo "添加信息成功";
    }
} else {
    echo "无效的请求方法";
}
