<?php
require "config.php";

// 设置每页显示的行数
$rowsPerPage = 7;

// 获取当前页数
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = (int)$_GET['page'];
} else {
    $currentPage = 1;
}

// 计算起始行数
$startRow = ($currentPage - 1) * $rowsPerPage;

// 查询数据库获取指定范围的数据
$query = "SELECT * FROM home LIMIT $startRow, $rowsPerPage";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('数据库查询失败: ' . mysqli_error($conn));
}

// 计算总页数
$totalRows = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM home"));
$totalPages = ceil($totalRows / $rowsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站首页</title>
    <link rel="stylesheet" href="./css/home.css">
</head>

<body>
    <h2>首页管理</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>描述</th>
                <th>图片</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['home_id'] . '</td>';
                echo '<td data-column-name="home_name" data-editable="true">' . htmlspecialchars($row['home_name']) . '</td>';
                echo '<td data-column-name="home_description" data-editable="true">' . htmlspecialchars($row['home_description']) . '</td>';
                echo '<td><img src="../' . $row['home_image_url'] . '" alt="Image" style="max-width: 100px; max-height: 100px;"></td>';
                echo '<td class="action-buttons">';
                echo '<a href="#" class="edit-button" data-id="' . $row['home_id'] . '">修改</a>';
                echo '<a href="#" class="delete-button" data-id="' . $row['home_id'] . '">删除</a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        // 显示上一页按钮
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '">上一页</a>';
        }
        // 显示页数
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
        // 显示下一页按钮
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '">下一页</a>';
        }
        ?>
    </div>

    <!-- 模态框 -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>修改信息</h3>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editId" name="id">
                <label for="editName">名称:</label>
                <input type="text" id="editName" name="name">
                <label for="editDescription">描述:</label>
                <input type="text" id="editDescription" name="description">
                <label for="editImage">图片:</label>
                <input type="file" id="editImage" name="image">
                <div id="imagePreview"></div> <!-- 用于预览图片的区域 -->
                <button type="submit">保存</button>
            </form>
        </div>
    </div>

    <script src="./js/jquery3.6.3.js"></script>
    <script>
        $(document).ready(function() {
            $("#editImage").change(function() {
                var input = this;
                var url = URL.createObjectURL(input.files[0]);
                $("#imagePreview").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
            });
            // 弹出模态框
            $(".edit-button").click(function() {
                try {
                    var id = $(this).data("id");
                    var name = $(this).closest("tr").find("[data-column-name='home_name']").text();
                    var description = $(this).closest("tr").find("[data-column-name='home_description']").text();

                    $("#editId").val(id);
                    $("#editName").val(name);
                    $("#editDescription").val(description);

                    $("#myModal").css("display", "block");
                } catch (error) {
                    console.error("An error occurred while opening the modal:", error);
                }
            });

            // 关闭模态框
            $(".close").click(function() {
                $("#myModal").css("display", "none");
            });

            // 提交表单（使用Ajax）
            $("#editForm").submit(function(e) {
                e.preventDefault();

                var id = $('#editId').val();
                var name = $('#editName').val();
                var description = $('#editDescription').val();
                var imageFile = $('#editImage')[0].files[0]; // 获取文件对象

                if (!imageFile) {
                    alert("请选择一个有效的图片文件");
                    return;
                }

                var formData = new FormData();
                formData.append('id', id);
                formData.append('name', name);
                formData.append('description', description);
                formData.append('image', imageFile); // 添加文件字段

                console.log("Before Ajax request");
                $.ajax({
                    url: "home_update.php",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        alert("修改成功！");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log("Ajax request error:", xhr, status, error);
                        alert("修改失败： " + xhr.responseText);
                    }
                });
            });



            // 删除数据（使用Ajax）
            $(".delete-button").click(function() {
                var id = $(this).data("id");

                if (confirm("确定要删除这条数据吗？")) {
                    $.ajax({
                        url: "home_delete.php",
                        method: "POST",
                        data: {
                            id: id
                        },
                        success: function(response) {
                            alert("删除成功！");
                            // 刷新页面
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert("删除失败： " + xhr.responseText);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>