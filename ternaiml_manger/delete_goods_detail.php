<?php
// delete_goods_detail.php
require "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 连接数据库

    // 获取POST请求中的数据
    if (isset($_POST['goodsid'])) {
        // 然后在这里使用 $_POST['goodsid']
        $goodsId = $_POST['goodsid'];

        // 接下来处理你的 SQL 查询等操作
    } else {
        // 如果不存在，输出错误信息或采取适当的处理方式
        echo "Error: 'goodsid' is not set in the POST data.";
    }


    // 执行删除操作
    $sql = "DELETE FROM goods_detail WHERE goods_id = $goodsId";

    if ($conn->query($sql) === TRUE) {
        $response = array(
            'status' => 'success',
            'message' => '商品删除成功'
        );
        echo json_encode($response);
    } else {
        $response = array(
            'status' => 'error',
            'message' => '商品删除失败: ' . $conn->error
        );
        echo json_encode($response);
    }

    // 关闭数据库连接
    $conn->close();
} else {
    // 如果不是POST请求，返回错误信息
    $response = array(
        'status' => 'error',
        'message' => '无效的请求方法'
    );
    echo json_encode($response);
}
