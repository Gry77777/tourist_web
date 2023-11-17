<?php
require "config.php";

// 从表单获取数据
$user_id = $_POST['user_id'];
$new_username = $_POST['username'];  // 新的用户名
$password = $_POST['password'];

$gender = $_POST['gender'];

// 处理上传的文件
$file_type = $_FILES['profile_pic']['type']; // 文件类型
if ($file_type == 'image/jpeg' || $file_type == 'image/png') {
    $targetDir = "img/"; // 上传文件存储目录
    $targetFile = $targetDir . basename($_FILES["profile_pic"]["name"]); // 目标文件路径
    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile); // 将临时文件移动到目标位置
} else {
    echo "文件类型不支持，仅支持JPEG和PNG格式的图片";
    exit();
}
$gender = ($_POST['gender'] === 'male') ? 0 : 1;
// 哈希处理密码
$password = password_hash($password, PASSWORD_DEFAULT);

// 开始一个事务
$conn->begin_transaction();

// 首先更新用户名
$update_username = $conn->prepare("UPDATE users SET username=? WHERE id=?");
$update_username->bind_param("si", $new_username, $user_id);
$update_username_success = $update_username->execute();

// 然后更新其他信息
if ($update_username_success) {
    $update_info = $conn->prepare("UPDATE users SET password=?, sex=?, img=? WHERE id=?");
    $update_info->bind_param("sssi", $password, $gender, $targetFile, $user_id);
    $update_info_success = $update_info->execute();
} else {
    echo "Error updating username: " . $update_username->error;
}

// 如果两个更新都成功，则提交事务，否则回滚事务
if ($update_username_success && $update_info_success) {
    $conn->commit();
    echo "用户信息更新成功";
} else {
    $conn->rollback();
    echo "Error updating user information";
}

// 关闭预处理语句和数据库连接
$update_username->close();
$update_info->close();
$conn->close();
