<?php
require "config.php";

// 每页显示的评论数量
$commentsPerPage = 5;

// 获取当前页码
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $commentsPerPage;

// 获取搜索条件和日期条件
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';

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

// 添加 LIMIT 和 OFFSET 子句
$sql .= " LIMIT $commentsPerPage OFFSET $offset";

$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

// 获取总评论数
$totalComments = $result->num_rows;

// 计算总页数
$totalPages = ceil($totalComments / $commentsPerPage);

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
    <div class="pagination">
        <?php
        // 上一页按钮
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '">上一页</a>';
        }

        // 页码链接
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }

        // 下一页按钮
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '">下一页</a>';
        }
        ?>
    </div>
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