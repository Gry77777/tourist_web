<?php
require "config.php";

// 获取评论、用户名、旅游地名，并添加模糊查询条件和日期条件
$searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // 假设你通过 GET 参数传递搜索条件
$dateFilter = isset($_GET['date']) ? $_GET['date'] : ''; // 假设你通过 GET 参数传递日期条件

$sql = "SELECT c.comment_id, c.tourist_id, c.user_id, u.username, tp.name as tourist_name, c.comment_text, c.created_at
        FROM tourists_comments c
        JOIN users u ON c.user_id = u.id
        JOIN tourists_place tp ON c.tourist_id = tp.tourist_id";

// 根据是否有搜索条件和日期条件动态添加 WHERE 子句
if (!empty($searchTerm) || !empty($dateFilter)) {
    $sql .= " WHERE ";
    if (!empty($searchTerm)) {
        $sql .= "(u.username LIKE '%$searchTerm%' OR tp.name LIKE '%$searchTerm%' OR c.comment_text LIKE '%$searchTerm%')";
    }
    if (!empty($searchTerm) && !empty($dateFilter)) {
        $sql .= " AND ";
    }
    if (!empty($dateFilter)) {
        $sql .= "DATE(c.created_at) = '$dateFilter'";
    }
}

$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>旅游评论管理</title>
    <script src="./js/jquery3.6.3.js"></script>
    <link rel="stylesheet" href="./css/tourist_comment.css">

</head>

<body>
    <h2>旅游详情页评论管理</h2>
    <form method="GET" action="">
        <label for="search">搜索：</label>
        <input type="text" id="search" placeholder="景区/用户名/内容搜索" name="search" />

        <label for="date">选择日期：</label>
        <input type="date" id="date" name="date" />

        <input type="submit" value="搜索" />
    </form>

    <table>
        <thead>
            <tr>
                <th>Tourist Name</th>
                <th>User Name</th>
                <th>Comment Text</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($comment = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $comment['tourist_name'] . '</td>';
                echo '<td>' . $comment['username'] . '</td>';
                echo '<td>' . $comment['comment_text'] . '</td>';
                echo '<td>' . $comment['created_at'] . '</td>';
                echo '<td>';
                echo '<button class="delete-button" data-comment-id="' . $comment['comment_id'] . '">Delete</button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <script>
        $('body').on('click', '.delete-button', function() {
            const commentId = $(this).data('comment-id');
            if (confirm("确认删除这条评论吗？")) {
                handleDelete(commentId);
            }
        });

        function handleDelete(commentId) {
            // 使用AJAX发送删除请求
            $.ajax({
                type: 'POST',
                url: 'tourist_deleteComment.php',
                data: {
                    commentId: commentId
                },
                success: function(response) {
                    const responseData = JSON.parse(response);
                    if (responseData.status === "success") {
                        location.reload();
                        const row = $('[data-comment-id="' + commentId + '"]');
                        if (row.length) {
                            row.remove();
                            alert(responseData.message);
                        }
                    } else {
                        console.error('删除失败:', responseData.message);
                        alert('删除失败，请稍后重试');
                    }
                },
                error: function(error) {
                    console.error('删除失败:', error);
                    alert('删除失败，请稍后重试');
                }
            });
        }
    </script>

</body>

</html>