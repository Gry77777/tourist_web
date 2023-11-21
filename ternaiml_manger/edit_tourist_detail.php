<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $touristId = $_POST['tourist_id'];
    $title = $_POST['title'];
    $introduction = $_POST['introduction'];
    $phone = $_POST['phone'];
    $ticket = $_POST['ticket'];
    $transportation = $_POST['transportation'];
    $openingHours = $_POST['opening_hours'];

    // Check if at least one new image is provided
    $newImagesProvided = false;
    for ($i = 1; $i <= 3; $i++) {
        $imageKey = 'image' . $i;
        if (isset($_FILES[$imageKey]) && !empty($_FILES[$imageKey]['name'])) {
            $newImagesProvided = true;
            $targetDirectory = "../tourist_img/";

            // 确保目标目录存在
            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            // 生成一个唯一的文件名
            $uniqueFilename = uniqid() . '_' . $_FILES[$imageKey]['name'];
            $targetFile = $targetDirectory . $uniqueFilename;
            echo "Target file: " . $targetFile . "<br>";

            if (move_uploaded_file($_FILES[$imageKey]['tmp_name'], $targetFile)) {
                echo "File moved successfully<br>";
                $sqlImage = "UPDATE tourist_details SET $imageKey = '$targetFile' WHERE tourist_id = $touristId";
                if ($conn->query($sqlImage) === TRUE) {
                    echo "Image update success<br>";
                } else {
                    echo "Error updating image: " . $conn->error . "<br>";
                }
            } else {
                echo "Error moving file<br>";
            }
        }
    }

    // Update other fields in the database only if new images are provided
    if ($newImagesProvided || (!$newImagesProvided && empty($_FILES['image1']['name']) && empty($_FILES['image2']['name']) && empty($_FILES['image3']['name']))) {
        $sqlUpdate = "UPDATE tourist_details SET
            title = '$title',
            introduction = '$introduction',
            phone = '$phone',
            ticket = '$ticket',
            transportation = '$transportation',
            opening_hours = '$openingHours'
            WHERE tourist_id = $touristId";

        if ($conn->query($sqlUpdate) === TRUE) {
            // 输出SQL Update Query
            echo "SQL Update Query: " . $sqlUpdate . "<br>";
            echo "success";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "No new images provided";
    }
} else {
    echo "Invalid request method";
}

$conn->close();
