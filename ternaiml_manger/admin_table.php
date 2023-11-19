<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        $adminId = $_POST["admin_id"];

        if ($_POST["action"] == "delete") {
            // 删除管理员
            $deleteQuery = "DELETE FROM admin WHERE admin_id = $adminId";
            if ($conn->query($deleteQuery) === TRUE) {
                echo json_encode(["status" => "success", "message" => "管理员删除成功"]);
            } else {
                echo json_encode(["status" => "error", "message" => "管理员删除失败"]);
            }
        } elseif ($_POST["action"] == "set_superadmin") {
            // 设置为超级管理员
            $updateQuery = "UPDATE admin SET is_superadmin = 1 WHERE admin_id = $adminId";
            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(["status" => "success", "message" => "管理员设置为超级管理员成功"]);
            } else {
                echo json_encode(["status" => "error", "message" => "管理员设置为超级管理员失败"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "未知操作"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "缺少操作参数"]);
    }

    $conn->close();
    exit;
}

$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

$conn->close();
