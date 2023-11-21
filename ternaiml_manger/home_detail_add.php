<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'], $_POST['name'], $_POST['description'], $_POST['recommended_season'], $_POST['suggested_stay_duration'], $_POST['main_attractions'], $_POST['featured_activities'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $recommended_season = mysqli_real_escape_string($conn, $_POST['recommended_season']);
        $suggested_stay_duration = mysqli_real_escape_string($conn, $_POST['suggested_stay_duration']);
        $main_attractions = mysqli_real_escape_string($conn, $_POST['main_attractions']);
        $featured_activities = mysqli_real_escape_string($conn, $_POST['featured_activities']);

        // 检查ID是否已经存在
        $checkIdQuery = "SELECT id FROM home_detail WHERE id = '$id'";
        $checkIdResult = mysqli_query($conn, $checkIdQuery);
        if (mysqli_num_rows($checkIdResult) > 0) {

            echo 'id重复，请尝试其他id';
            exit;
        }

        $targetFile = ""; // 初始化为空字符串

        // 检查是否上传了图像
        if (isset($_FILES["addImage"]) && $_FILES["addImage"]["size"] > 0) {
            // 处理上传的图像
            $targetDir = "../home_img/";
            $fileName = basename($_FILES["addImage"]["name"]);
            $targetFile = $targetDir . md5(uniqid()) . "_" . $fileName;
            $allowedTypes = array("image/jpeg", "image/png", "image/gif");
            $maxSize = 1024 * 1024; // 1MB
            if (in_array($_FILES["addImage"]["type"], $allowedTypes) && $_FILES["addImage"]["size"] <= $maxSize) {
                move_uploaded_file($_FILES["addImage"]["tmp_name"], $targetFile);
            } else {
                // 返回错误信息到前端
                echo "上传的文件不符合要求";
                exit;
            }
        }

        // 将数据插入数据库
        $sql = "INSERT INTO home_detail (id, name, description, image_url, recommended_season, suggested_stay_duration, main_attractions, featured_activities) VALUES ('$id', '$name', '$description', '$targetFile', '$recommended_season', '$suggested_stay_duration', '$main_attractions', '$featured_activities')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // 返回成功信息到前端
            echo "添加成功";
        } else {
            // 返回错误信息到前端
            echo "添加失败: " . mysqli_error($conn);
        }
    } else {
        // 返回错误信息到前端
        echo "<script>alert('缺少必要参数);</script>";
    }
} else {
    // 返回错误信息到前端
    echo "非法请求";
}
