<?php
// 引入数据库连接配置
require "config.php";

// 处理密码重置
// if (isset($_POST['resetPassword'])) {
//     $userId = $_POST['user_id'];
//     // 重置密码为默认密码（示例中设置为 "123456"）
//     $hashedPassword = password_hash("123456", PASSWORD_DEFAULT);
//     $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = '$userId'";
//     $conn->query($updateQuery);
// }

// // 处理删除账号
// if (isset($_POST['deleteAccount'])) {
//     $userId = $_POST['user_id'];
//     $deleteQuery = "DELETE FROM users WHERE id = '$userId'";
//     $conn->query($deleteQuery);
// }

$searchUsername = isset($_GET['searchUsername']) ? '%' . $_GET['searchUsername'] . '%' : '';
$searchGender = isset($_GET['searchGender']) ? $_GET['searchGender'] : '';

// 构建查询条件
$searchCondition = "";
if (!empty($searchUsername)) {
    $searchCondition .= " AND username LIKE '$searchUsername'";
}
if (!empty($searchGender)) {
    $searchCondition .= " AND sex = '$searchGender'";
}

// 分页
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$recordsPerPage = 5;
$offset = ($page - 1) * $recordsPerPage;

// 查询当前页的用户数据（包括搜索条件）
$query = "SELECT * FROM users WHERE 1 $searchCondition LIMIT $offset, $recordsPerPage";
$result = $conn->query($query);

// 查询总记录数（包括搜索条件）
$totalRecordsQuery = "SELECT COUNT(*) as total FROM users WHERE 1 $searchCondition";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户信息管理</title>
    <link rel="stylesheet" href="./css/user_manger.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
    <h2>用户信息管理</h2>
    
    <form method="get" action="">
        <label for="searchUsername">用户名：</label>
        <input type="text" id="searchUsername" name="searchUsername">

        <label for="searchGender">性别：</label>
        <select id="searchGender" name="searchGender">
            <option value="">全部</option>
            <option value="1">女</option>
            <option value="0">男</option>
        </select>

        <button type="submit" name="search">搜索</button>
    </form>

    <table>
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>用户名</th>
                <th>密码</th>
                <th>头像</th>
                <th>性别</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <!-- <td><?/*php echo $row['id']; */ ?></td> -->
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['password']; ?></td>
                    <td> <img src="../<?php echo $row['img']; ?>" alt="头像" style="width: 50px; height: 50px;"></td>
                    <td><?php echo ($row['sex'] == 1) ? '女' : '男'; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">

                            <!-- 在每个操作按钮后添加显示结果的元素 -->
                            <!-- 重置按钮 -->
                            <button type="submit" class="reset-btn" data-userid="<?php echo $row['id']; ?>">重置密码</button>
                            <div class="reset-buttons">
                                <button type="submit" class="reset-confirm-btn" data-userid="<?php echo $row['id']; ?>" style="display: none;">确定</button>
                                <button type="submit" class="reset-cancel-btn" data-userid="<?php echo $row['id']; ?>" style="display: none;">取消</button>
                            </div>
                            <span class="reset-result" id="reset-result-<?php echo $row['id']; ?>"></span>

                            <!-- 删除按钮 -->
                            <button type="submit" class="delete-btn" data-userid="<?php echo $row['id']; ?>">删除账号</button>
                            <div class="delete-buttons">
                                <button type="submit" class="delete-confirm-btn" data-userid="<?php echo $row['id']; ?>" style="display: none;">确定</button>
                                <button type="submit" class="delete-cancel-btn" data-userid="<?php echo $row['id']; ?>" style="display: none;">取消</button>
                            </div>
                            <span class="delete-result" id="delete-result-<?php echo $row['id']; ?>"></span>



                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo ($page - 1); ?>">上一页</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages) : ?>
            <a href="?page=<?php echo ($page + 1); ?>">下一页</a>
        <?php endif; ?>
    </div>
</body>
<script>
    $(document).ready(function() {
        // Reset buttons
        $('.reset-btn').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            var userId = $(this).data('userid');
            $('.reset-confirm-btn[data-userid="' + userId + '"]').show();
            $('.reset-cancel-btn[data-userid="' + userId + '"]').show();
            $(this).hide();
        });

        $('.reset-cancel-btn').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            var userId = $(this).data('userid');
            $('.reset-btn[data-userid="' + userId + '"]').show();
            $('.reset-confirm-btn[data-userid="' + userId + '"]').hide();
            $(this).hide();
        });

        // Delete buttons
        $('.delete-btn').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            var userId = $(this).data('userid');
            $('.delete-confirm-btn[data-userid="' + userId + '"]').show();
            $('.delete-cancel-btn[data-userid="' + userId + '"]').show();
            $(this).hide();
        });

        $('.delete-cancel-btn').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            var userId = $(this).data('userid');
            $('.delete-btn[data-userid="' + userId + '"]').show();
            $('.delete-confirm-btn[data-userid="' + userId + '"]').hide();
            $(this).hide();
        });
    });



    // 确认按钮点击事件
    $('.reset-confirm-btn').click(function() {
        var userId = $(this).data('userid');
        // 发送 AJAX 请求进行密码重置
        $.ajax({
            type: 'POST',
            url: 'reset_password.php',
            data: {
                user_id: userId
            },
            success: function(response) {
                // 处理密码重置成功的情况
                alert('密码重置成功');
            },
            error: function() {
                // 处理密码重置失败的情况
                alert('密码重置失败');
            },
            complete: function() {
                // 隐藏确认和取消按钮，显示原来的按钮
                $(`.reset-btn[data-userid=${userId}]`).show();
                $(`.reset-confirm-btn[data-userid=${userId}], .reset-cancel-btn[data-userid=${userId}]`).hide();
            }
        });
    });

    // 确认按钮点击事件
    $('.delete-confirm-btn').click(function() {
        var userId = $(this).data('userid');
        // 发送 AJAX 请求进行账号删除
        $.ajax({
            type: 'POST',
            url: 'delete_account.php',
            data: {
                user_id: userId
            },
            success: function(response) {
                // 处理账号删除成功的情况
                alert('账号删除成功');
            },
            error: function() {
                // 处理账号删除失败的情况
                alert('账号删除失败');
            },
            complete: function() {
                // 隐藏确认和取消按钮，显示原来的按钮
                $(`.delete-btn[data-userid=${userId}]`).show();
                $(`.delete-confirm-btn[data-userid=${userId}], .delete-cancel-btn[data-userid=${userId}]`).hide();
            }
        });
    });
</script>

</html>