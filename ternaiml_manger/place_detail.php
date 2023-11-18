<?php
require "config.php";

$introductionData = []; // Define the array before the loop

$introductionQuery = "SELECT introduction_id, region_id, title, content FROM region_introductions";
$introductionResult = $conn->query($introductionQuery);

while ($introduction = $introductionResult->fetch_assoc()) {
    $regionId = $introduction['region_id'];
    // Query region_images table to get the corresponding image_url
    $imageQuery = "SELECT image_url FROM region_images WHERE region_id = $regionId";
    $imageResult = $conn->query($imageQuery);
    $imageData = $imageResult->fetch_assoc();
    $imageUrl = $imageData ? $imageData['image_url'] : ''; // If the query result is empty, set imageUrl to an empty string

    $introductionData[] = [
        'introduction_id' => $introduction['introduction_id'],
        'region_id' => $regionId,
        'title' => htmlspecialchars($introduction['title']),
        'content' => htmlspecialchars($introduction['content']),
        'image_url' => $imageUrl
    ];
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Place Detail Information</title>
    <link rel="stylesheet" href="./css/place_detail.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
    <h2>地区详情管理</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Image URL</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($introductionData as $data) : ?>
            <tr>
                <td contenteditable="false"><?= $data['title'] ?></td>
                <td contenteditable="false"><?= $data['content'] ?></td>
                <td><img src="../<?= $data['image_url'] ?>" alt=""></td>
                <td>
                    <button class="edit-btn" onclick="editIntroduction(this)" data-original-title="<?= $data['title'] ?>" data-original-content="<?= $data['content'] ?>" data-introduction-id="<?= $data['introduction_id'] ?>">修改</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- 拟态框 -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modal-title">修改信息</h3>
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                <label for="edit-title">名称:</label>
                <input type="text" id="edit-title" name="edit-title">
                <label for="edit-content">描述:</label>
                <input type="text" id="edit-content" name="edit-content">
                <label for="edit-image">图片:</label>
                <input type="file" id="edit-image" name="edit-image">
                <div id="imagePreview"></div> <!-- 用于预览图片的区域 -->
                <input type="submit" value="保存">
            </form>
        </div>
    </div>


    <script>
        $("#edit-image").change(function() {
            var input = this;
            var url = URL.createObjectURL(input.files[0]);
            $("#imagePreview").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
        });
        // 修改按钮点击事件
        function editIntroduction(button) {
            var title = $(button).data("original-title");
            var content = $(button).data("original-content");
            var introductionId = $(button).data("introduction-id");

            // 设置拟态框标题和内容
            $("#modal-title").text("编辑");
            $("#edit-title").val(title);
            $("#edit-content").val(content);

            // 显示拟态框
            $("#modal").css("display", "block");

            // 保存按钮点击事件
            $("#edit-form").off("submit").on("submit", function(e) {
                e.preventDefault();

                var newTitle = $("#edit-title").val();
                var newContent = $("#edit-content").val();

                // 创建 FormData 对象，用于上传文件
                var formData = new FormData();
                formData.append("introductionId", introductionId);
                formData.append("newTitle", newTitle);
                formData.append("newContent", newContent);
                // 检查是否有选择文件
                var imageFile = $('#edit-image')[0].files[0];
                if (imageFile) {
                    formData.append('edit-image', imageFile);
                }
                console.log("FormData content:");
                formData.forEach(function(value, key) {
                    console.log(key, value);
                });

                // 发送Ajax请求进行修改
                $.ajax({
                    url: "place_detail_update.php",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        console.log(response);
                        location.reload();
                        alert("修改成功！");
                        // 隐藏拟态框
                        $("#modal").hide();
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        }

        // 删除按钮点击事件
        function confirmDelete(button) {
            var introductionId = $(button).data("introduction-id");

            if (confirm("确认删除该项？")) {
                // 发送Ajax请求进行删除
                $.ajax({
                    url: "place_detail_delete.php", // 删除数据的后端处理接口
                    method: "POST",
                    data: {
                        introductionId: introductionId
                    },
                    success: function(response) {
                        // 处理成功回调
                        console.log(response);
                        // 从表格中移除对应的行
                        $(button).parent().parent().remove();
                    },
                    error: function(xhr, status, error) {
                        // 处理错误回调
                        console.log(xhr.responseText);
                    }
                });
            }
        }

        // 拟态框关闭按钮点击事件
        $(".close").click(function() {
            $("#modal").css("display", "none");
        });
    </script>

</body>

</html>