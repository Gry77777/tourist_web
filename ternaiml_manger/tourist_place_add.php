<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 获取传入的数据
    $touristId = isset($_POST['tourist_id']) ? $_POST['tourist_id'] : null;
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    // 检查是否提供了图片文件
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../tourists_img/"; // 指定目标目录
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 检查文件是否为图片
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // 将上传的文件移动到目标目录
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // 检查 tourist_id 是否已存在
                $checkIdQuery = "SELECT COUNT(*) as count FROM tourists_place WHERE tourist_id='$touristId'";
                $idResult = $conn->query($checkIdQuery);
                $idCount = $idResult->fetch_assoc()['count'];

                if ($idCount > 0) {
                    echo "该 Tourist ID 已存在，请重新写。";
                } else {
                    // 插入带有图片的新 tourist 数据
                    $insertQuery = "INSERT INTO tourists_place (tourist_id, name, img) VALUES ('$touristId', '$name', '$targetFile')";
                    if ($conn->query($insertQuery) === TRUE) {
                        echo "Tourist 添加成功！";
                    } else {
                        echo "添加 tourist 时出错：" . $conn->error;
                    }
                }
            } else {
                echo "上传文件时出错。";
            }
        } else {
            echo "文件不是图片。";
        }
    } else {
        // 如果没有提供图片，则插入不带图片的新数据
        // 检查 tourist_id 是否已存在
        $checkIdQuery = "SELECT COUNT(*) as count FROM tourists_place WHERE tourist_id='$touristId'";
        $idResult = $conn->query($checkIdQuery);
        $idCount = $idResult->fetch_assoc()['count'];

        if ($idCount > 0) {
            echo "该 Tourist ID 已存在，请重新写。";
        } else {
            // 插入不带图片的新 tourist 数据
            $insertQuery = "INSERT INTO tourists_place (tourist_id, name) VALUES ('$touristId', '$name')";
            if ($conn->query($insertQuery) === TRUE) {
                echo "Tourist 添加成功！";
            } else {
                echo "添加 tourist 时出错：" . $conn->error;
            }
        }
    }
}

$conn->close();
