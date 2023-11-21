<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'], $_POST['name'], $_POST['description'], $_POST['recommended_season'], $_POST['suggested_stay_duration'], $_POST['main_attractions'], $_POST['featured_activities'])) {
        $id = $_POST['id'];
        $modifiedName = mysqli_real_escape_string($conn, $_POST['name']);
        $modifiedDescription = mysqli_real_escape_string($conn, $_POST['description']);
        $modifiedRecommendedSeason = mysqli_real_escape_string($conn, $_POST['recommended_season']);
        $modifiedSuggestedStayDuration = mysqli_real_escape_string($conn, $_POST['suggested_stay_duration']);
        $modifiedMainAttractions = mysqli_real_escape_string($conn, $_POST['main_attractions']);
        $modifiedFeaturedActivities = mysqli_real_escape_string($conn, $_POST['featured_activities']);

        $targetFile = ""; // Initialize with an empty string

        // Check if an image is uploaded
        if (isset($_FILES["modifyImage"]) && $_FILES["modifyImage"]["size"] > 0) {
            // Handle the uploaded image
            $targetDir = "../home_img/";
            $fileName = basename($_FILES["modifyImage"]["name"]);
            $targetFile = $targetDir . md5(uniqid()) . "_" . $fileName;
            $allowedTypes = array("image/jpeg", "image/png", "image/gif");
            $maxSize = 1024 * 1024; // 1MB
            if (in_array($_FILES["modifyImage"]["type"], $allowedTypes) && $_FILES["modifyImage"]["size"] <= $maxSize) {
                move_uploaded_file($_FILES["modifyImage"]["tmp_name"], $targetFile);
            } else {
                echo "上传的文件不符合要求";
                exit;
            }
        }

        // Update database record
        $sql = "UPDATE home_detail SET name='$modifiedName', description='$modifiedDescription'";
        if (!empty($targetFile)) {
            $sql .= ", image_url='$targetFile'";
        }
        $sql .= ", recommended_season='$modifiedRecommendedSeason', suggested_stay_duration='$modifiedSuggestedStayDuration', main_attractions='$modifiedMainAttractions', featured_activities='$modifiedFeaturedActivities' WHERE id=$id";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "修改成功";
        } else {
            echo "修改失败";
        }
    } else {
        echo "缺少必要的参数";
    }
} else {
    echo "非法请求";
}
