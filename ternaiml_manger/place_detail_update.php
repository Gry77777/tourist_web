<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $introductionId = $_POST['introductionId'];
    $newTitle = $_POST['newTitle'];
    $newContent = $_POST['newContent'];


    // 判断是否有上传图片
    if (isset($_FILES["edit-image"]) && $_FILES["edit-image"]["size"] > 0) {
        // 处理图像上传
        $targetDir = "../tourists_img/";
        $targetFile = $targetDir . basename($_FILES["edit-image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 检查文件是否是真实图像
        $check = getimagesize($_FILES["edit-image"]["tmp_name"]);
        if ($check === false) {
            echo "文件不是图像。";
            $uploadOk = 0;
        }

        // 检查文件是否已经存在
        if (file_exists($targetFile)) {
            echo "抱歉，文件已经存在。";
            $uploadOk = 0;
        }

        // 检查文件大小
        if ($_FILES["edit-image"]["size"] > 500000) {
            echo "抱歉，您的文件太大。";
            $uploadOk = 0;
        }

        // 允许特定的文件格式
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "抱歉，仅允许 JPG、JPEG、PNG 和 GIF 文件。";
            $uploadOk = 0;
        }

        // 检查 $uploadOk 是否由错误设置为 0
        if ($uploadOk == 0) {
            echo "抱歉，您的文件未上传。";
        } else {
            // 如果一切正常，尝试上传文件
            if (move_uploaded_file($_FILES["edit-image"]["tmp_name"], $targetFile)) {
                echo "文件 " . htmlspecialchars(basename($_FILES["edit-image"]["name"])) . " 已成功上传。";

                // 使用新的图像 URL 更新 region_images 表
                $updateQueryImages = "UPDATE region_images SET image_url = ? WHERE region_id = ?";
                $stmtImages = $conn->prepare($updateQueryImages);
                $stmtImages->bind_param("si", $targetFile, $introductionId); // 使用 region_id
                $stmtImages->execute();
                $stmtImages->close();

                // 使用新数据（包括图像 URL）更新 region_introductions 表
                $updateQueryIntroductions = "UPDATE region_introductions SET title = ?, content = ? WHERE introduction_id = ?";
                $stmtIntroductions = $conn->prepare($updateQueryIntroductions);
                $stmtIntroductions->bind_param("ssi", $newTitle, $newContent, $introductionId);
                $stmtIntroductions->execute();
                $stmtIntroductions->close();
            } else {
                echo "抱歉，上传文件时出现错误。";
            }
        }
    } else {
        // 如果没有上传图片，则仅更新 region_introductions 表中的标题和内容
        $updateQueryIntroductions = "UPDATE region_introductions SET title = ?, content = ? WHERE introduction_id = ?";
        $stmtIntroductions = $conn->prepare($updateQueryIntroductions);
        $stmtIntroductions->bind_param("ssi", $newTitle, $newContent, $introductionId);
        $stmtIntroductions->execute();
        $stmtIntroductions->close();
    }
}

$conn->close();
