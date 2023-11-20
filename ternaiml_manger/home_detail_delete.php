<?php
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM home_detail WHERE id = $id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "删除成功";
            // 或者返回其他有意义的信息
        } else {
            echo "删除失败";
            // 或者返回其他有意义的信息
        }
    }
} else {
    echo "非法请求";
}
