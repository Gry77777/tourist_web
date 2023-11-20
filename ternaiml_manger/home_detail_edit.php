<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['modifyID'], $_POST['modifyName'], $_POST['modifyDescription'], $_POST['modifyRecommendedSeason'], $_POST['modifySuggestedStayDuration'], $_POST['modifyMainAttractions'], $_POST['modifyFeaturedActivities'])) {
        $id = $_POST['modifyID'];
        $modifiedName = mysqli_real_escape_string($conn, $_POST['modifyName']);
        $modifiedDescription = mysqli_real_escape_string($conn, $_POST['modifyDescription']);
        $modifiedRecommendedSeason = mysqli_real_escape_string($conn, $_POST['modifyRecommendedSeason']);
        $modifiedSuggestedStayDuration = mysqli_real_escape_string($conn, $_POST['modifySuggestedStayDuration']);
        $modifiedMainAttractions = mysqli_real_escape_string($conn, $_POST['modifyMainAttractions']);
        $modifiedFeaturedActivities = mysqli_real_escape_string($conn, $_POST['modifyFeaturedActivities']);

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
        $sql = "UPDATE home_detail SET name=?, description=?,";
        if (!empty($targetFile)) {
            $sql .= " image_url=?,";
        }
        $sql .= " recommended_season=?, suggested_stay_duration=?, main_attractions=?, featured_activities=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $modifiedName, $modifiedDescription);
        if (!empty($targetFile)) {
            mysqli_stmt_bind_param($stmt, "s", $targetFile);
        }
        mysqli_stmt_bind_param($stmt, "ssssssi", $modifiedRecommendedSeason, $modifiedSuggestedStayDuration, $modifiedMainAttractions, $modifiedFeaturedActivities, $id);
        $result = mysqli_stmt_execute($stmt);

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
