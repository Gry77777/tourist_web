<?php
require "config.php";
session_start();  // 启动会话
// 检查是否存在用户登录状态以及必要的用户信息
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (empty($user_id) || empty($username)) {
    $readOnly = true;  // 如果没有用户登录状态或者缺少必要的用户信息，则设置为只读模式
} else {
    $readOnly = false;  // 如果有用户登录状态且包含必要的用户信息，则允许修改
    $user_id = $_SESSION['user_id'];
    $query = "SELECT username FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // 从结果集中获取用户名
        $row = $result->fetch_assoc();
        $username = $row['username'];
    } else {
        $username = ''; // 如果未找到用户，可以提供一个默认值
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心</title>
    <link rel="stylesheet" href="./css/personal.css">
    <script src="./js/jquery3.6.3.js"></script>
    <script>
        var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        console.log(userId);
    </script>
    <script src="./js/personal.js"></script>
</head>

<body>
    <div class="top">
        <img src="./image/user_big.png" alt="">
        <h2>
            个人中心
        </h2>
    </div>

    <div class="main">
        <form id="personalForm" enctype="multipart/form-data">
            <?php if ($readOnly) : ?>
                <style>
                    #personalForm input,
                    #personalForm button,
                    #personalForm textarea,
                    #personalForm select {
                        pointer-events: none;
                        /* 设置为只读 */
                        background-color: #f4f4f4;
                        /* 修改背景颜色为灰色，显示为只读状态 */
                    }
                </style>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">用户名：</label>
                <input type="text" id="username" name="username" value="<?php echo "$username" ?>" required>
                <span id="username_exists_message" style="color: red;"></span>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">确认密码：</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label>性别：</label>
                <input type="radio" id="male" name="gender" value="male" requird>
                <label for="male">男</label>
                <input type="radio" id="female" name="gender" value="female" required>
                <label for="female">女</label>
            </div>
            <div class="form-group">
                <label for="profile_pic">上传头像：</label>
                <input type="file" id="profile_pic" name="profile_pic" onchange="previewImage(event)" required>
                <img id="preview" src="" alt="Preview Image" style="max-width: 200px;">
            </div>
            <button type="submit">确认修改</button>
        </form>
    </div>

</body>

</html>