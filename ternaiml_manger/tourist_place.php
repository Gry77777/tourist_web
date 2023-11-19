<?php
require "config.php";

$itemsPerPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$touristsData = [];
$touristsQuery = "SELECT tourist_id, name, img FROM tourists_place ";

// 添加模糊查询条件
if (!empty($searchTerm)) {
    $touristsQuery .= "WHERE name LIKE '%$searchTerm%' ";
}

$touristsQuery .= "LIMIT " . (($page - 1) * $itemsPerPage) . ", $itemsPerPage";
$touristsResult = $conn->query($touristsQuery);

while ($tourist = $touristsResult->fetch_assoc()) {
    $touristsData[] = [
        'tourist_id' => $tourist['tourist_id'],
        'name' => htmlspecialchars($tourist['name']),
        'img' => htmlspecialchars($tourist['img'])
    ];
}

$totalItemsQuery = "SELECT COUNT(*) AS total FROM tourists_place";

// 添加模糊查询条件
if (!empty($searchTerm)) {
    $totalItemsQuery .= " WHERE name LIKE '%$searchTerm%'";
}

$totalItemsResult = $conn->query($totalItemsQuery);
$totalItems = $totalItemsResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tourists Places Information</title>
    <link rel="stylesheet" href="./css/tourist_place.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
    <h2>景点预览管理</h2>
    <form method="GET" action="" style="text-align: center;">
        <label for="search">搜索：</label>
        <input type="text" id="search" name="search" value="<?= $searchTerm ?>" />
        <input type="submit" value="搜索" />
    </form>
    <table border="1" id="yourTable">
        <tr>
            <th>Tourist ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($touristsData as $data) : ?>
            <tr>
                <td><?= $data['tourist_id'] ?></td>
                <td contenteditable="false"><?= $data['name'] ?></td>
                <td><img src="../<?= $data['img'] ?>" alt="<?= $data['name'] ?>" style="width: 200px; height: 150px;"></td>
                <td>
                    <button class="edit-btn" data-original-name="<?= $data['name'] ?>" data-tourist-id="<?= $data['tourist_id'] ?>">修改</button>
                    <button class="delete-btn" data-tourist-id="<?= $data['tourist_id'] ?>">删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modal-title">修改信息</h3>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                <label for="edit-title">ID:</label>
                <input type="text" id="edit-title" name="edit-title">
                <label for="edit-content">名字:</label>
                <input type="text" id="edit-content" name="edit-content">
                <label for="edit-image">图片:</label>
                <input type="file" id="edit-image" name="edit-image">
                <div id="imagePreview"></div> <!-- 用于预览图片的区域 -->
                <input type="submit" value="保存">
            </form>
        </div>
    </div>

    <button id="addTouristBtn">添加景点</button>
    <!-- Modal for adding new tourist -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" id="addClose">&times;</span>
            <h3 id="addModalTitle">添加景点信息</h3>
            <form id="add-form" method="POST" enctype="multipart/form-data">
                <label for="add-id">id:</label>
                <input type="text" id="add-id" name="add-id">
                <label for="add-content">名字:</label>
                <input type="text" id="add-content" name="add-content">
                <label for="add-image">图片:</label>
                <input type="file" id="add-image" name="add-image">
                <div id="addImagePreview"></div> <!-- Area for previewing the image -->
                <input type="submit" value="保存">
            </form>
        </div>
    </div>

    <div id="pagination">
        <button id="prevPage">上一页</button>
        <span id="currentPage"><?= $page ?></span>/<span id="totalPages"><?= ceil($totalItems / $itemsPerPage) ?></span>
        <button id="nextPage">下一页</button>
    </div>

    <script>
        $(document).ready(function() {
            $("#add-image").change(function() {
                var input = this;
                var url = URL.createObjectURL(input.files[0]);
                $("#addImagePreview").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
            });

            $("#edit-image").change(function() {
                var input = this;
                var url = URL.createObjectURL(input.files[0]);
                $("#imagePreview").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
            });

            const itemsPerPage = 7;
            let currentPage = <?= $page ?>;
            const totalPages = <?= ceil($totalItems / $itemsPerPage) ?>;

            function updatePageButtons() {
                $('#prevPage').prop('disabled', currentPage === 1);
                $('#nextPage').prop('disabled', currentPage === totalPages);
                $('#currentPage').text(currentPage);
                $('#totalPages').text(totalPages);
            }
            $('#prevPage').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    updatePageButtons();
                    loadPageData();
                }
            });
            $('#nextPage').on('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePageButtons();
                    loadPageData();
                }
            });
            //
            $(".edit-btn").click(function() {
                try {
                    var id = $(this).data("tourist-id"); // 修正为正确的数据属性
                    var name = $(this).closest("tr").find("td:eq(1)").text(); // 使用正确的列索引获取名字
                    // 如果需要获取其他列的数据，可以类似地使用相应的列索引
                    $("#edit-title").val(id);
                    $("#edit-content").val(name);
                    $("#modal").css("display", "block"); // 修正为正确的模态框 ID
                } catch (error) {
                    console.error("An error occurred while opening the modal:", error);
                }
            });

            // 关闭模态框
            $(".close").click(function() {
                $("#modal").css("display", "none"); // 修正为正确的模态框 ID
            });

            function loadPageData() {
                const url = `tourist_place.php?page=${currentPage}`;
                window.location.href = url;
            }
            updatePageButtons();

            $("#edit-form").submit(function(e) {
                e.preventDefault();

                var id = $('#edit-title').val();
                var name = $('#edit-content').val();
                var imageFile = $('#edit-image')[0].files[0];
                var formData = new FormData();
                formData.append('id', id);
                formData.append('name', name);

                // 添加文件字段，仅当选择了文件时才添加
                if (imageFile) {
                    formData.append('image', imageFile);
                }
                formData.forEach(function(value, key) {
                    console.log(key, value);
                });
                $.ajax({
                    url: "tourist_place_save.php", // 替换为你的保存处理脚本的路径
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        alert("保存成功！");
                        location.reload();
                        $("#modal").css("display", "none");
                        // 可以根据需要刷新页面或执行其他操作
                    },
                    error: function(xhr, status, error) {
                        console.log("Ajax request error:", xhr, status, error);
                        alert("保存失败： " + xhr.responseText);
                    }
                });
            });


            $(".delete-btn").click(function() {
                var confirmed = confirm("确认删除？");
                if (!confirmed) {
                    return;
                }

                var touristId = $(this).data("tourist-id");
                console.log(touristId);
                $.ajax({
                    url: "tourist_place_delete.php",
                    method: "POST",
                    data: {
                        touristId: touristId
                    },
                    success: function(response) {
                        console.log(response);
                        alert("删除成功！");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log("Ajax request error:", xhr, status, error);
                        alert("删除失败： " + xhr.responseText);
                    }
                });
            });


            $("#addTouristBtn").click(function() {
                $("#addModal").css("display", "block");
            });

            // Close the add modal when the close button is clicked
            $("#addClose").click(function() {
                $("#addModal").css("display", "none");
            });

            // Submit form to add new tourist
            $("#add-form").submit(function(e) {
                e.preventDefault();

                var name = $('#add-content').val();
                var imageFile = $('#add-image')[0].files[0];
                var touristId = $('#add-id').val();
                var formData = new FormData();
                formData.append('name', name);
                formData.append('tourist_id', touristId);
                // Add the file field only if a file is selected
                if (imageFile) {
                    formData.append('image', imageFile);
                }
                formData.forEach(function(value, key) {
                    console.log(key, value);
                });
                $.ajax({
                    url: "tourist_place_add.php", // Replace with your script for adding tourist
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.includes("已存在")) {
                            // 显示提示，例如使用 alert 或者将提示信息插入到页面中的一个元素
                            alert(response);
                        } else {
                            // 重载页面等其他操作
                            location.reload();
                            $("#addModal").css("display", "none");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Ajax request error:", xhr, status, error);
                        alert("添加失败： " + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>