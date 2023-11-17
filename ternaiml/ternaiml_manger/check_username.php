<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];

    // 检测用户名是否已存在
    $checkUsernameQuery = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);

    if ($result->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'not_exists';
    }

    $conn->close();
}
