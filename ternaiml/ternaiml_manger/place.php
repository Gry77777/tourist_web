<?php
require "config.php"; // 引入数据库配置文件

// 准备一个数组来存储所有数据
$regionsData = [];
// 获取 regions 表的数据
$regionsQuery = "SELECT region_id, name, description, image_url FROM regions";
$regionsResult = $conn->query($regionsQuery);
while ($region = $regionsResult->fetch_assoc()) {
    $regionsData[] = [
        'region_id' => $region['region_id'],
        'name' => htmlspecialchars($region['name']),
        'description' => htmlspecialchars($region['description']),
        'image_url' => htmlspecialchars($region['image_url'])
    ];
}
// 关闭数据库连接
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Region Information</title>
    <link rel="stylesheet" href="./css/place.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>

<body>
    <h2>地区预览图</h2>
    <table border="1">
        <tr>
            <th>Region ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image URL</th>
            <th>Change Image</th> <!-- 新增列 -->
            <th>Actions</th>
        </tr>
        <?php foreach ($regionsData as $data) : ?>
            <tr>
                <td><?= $data['region_id'] ?></td>
                <td contenteditable="false"><?= $data['name'] ?></td>
                <td contenteditable="false"><?= $data['description'] ?></td>
                <td>
                    <img src="../<?= $data['image_url'] ?>" alt="Region Image" height="100">
                </td>
                <td>
                    <!-- 添加一个文件上传的input -->
                    <input type="file" name="new_image" accept="image/*" style="display: none;" onchange="previewImage(this, <?= $data['region_id'] ?>)">
                </td>
                <td>
                    <button class="edit-btn" onclick="editRegion(this)" data-original-name="<?= $data['name'] ?>" data-original-description="<?= $data['description'] ?>" data-original-image="<?= $data['image_url'] ?>" data-region-id="<?= $data['region_id'] ?>">修改</button>
                    <button class="save-btn" style="display: none;" onclick="saveRegion(this)">保存</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                    <button class="confirm-delete-btn" style="display: none;" onclick="deleteRegion(<?= $data['region_id'] ?>)">确认删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editRegion(button) {
            var row = button.parentNode.parentNode;
            var editableCells = row.querySelectorAll('[contenteditable="false"]');
            editableCells.forEach(function(cell) {
                cell.contentEditable = true;
            });

            // 隐藏编辑按钮，显示保存按钮
            $(button).hide();
            $(button).siblings('.save-btn').show();

            // 显示文件上传的input
            $(button).closest('tr').find('input[type="file"]').show();
        }

        var originalImage;
        // 绑定点击事件，获取 data-original-image 值
        $(".edit-btn").click(function() {
            originalImage = $(this).data("original-image");
            // 在这里使用 originalImage 进行你的操作
            console.log("Original Image:", originalImage);
        });

        function saveRegion(button) {
            var row = button.parentNode.parentNode;
            var regionId = row.cells[0].innerText;
            var newName = row.cells[1].innerText;
            var newDescription = row.cells[2].innerText;

            // 处理图片上传
            var inputElement = row.querySelector('input[type="file"]');
            var newImage = inputElement.files[0];
            // 获取原始图片路径

            console.log("Original Image:", originalImage);
            var formData = new FormData();
            formData.append('region_id', regionId);
            formData.append('name', newName);
            formData.append('description', newDescription);
            formData.append('original_image_url', originalImage);
            // 只有在选择了新图片时才会添加 new_image 字段
            if (newImage) {
                formData.append('new_image', newImage);
            }

            // 发送保存请求
            $.ajax({
                url: 'place_save.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    alert(response.message);

                    // 将保存后的数据显示在表格中
                    // 这里的代码可以根据实际情况进行修改
                    row.cells[1].innerText = newName;
                    row.cells[2].innerText = newDescription;

                    // 检查 new_image_url 是否为空值
                    if (response.new_image_url !== '') {
                        row.cells[3].innerHTML = '<img src="../' + response.new_image_url + '" alt="Region Image" height="100">';
                    } else {
                        // 如果为空值，使用 originalImage 的值
                        row.cells[3].innerHTML = '<img src="../' + originalImage + '" alt="Region Image" height="100">';
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('保存区域时发生错误:', errorThrown);

                    // 输出详细错误信息到控制台
                    console.log(jqXHR.responseText);

                    alert('保存区域时发生错误，请查看控制台获取详细信息');
                }
            });
            // 将所有可编辑单元格的 contenteditable 属性设为 false
            var editableCells = row.querySelectorAll('[contenteditable="false"]');
            editableCells.forEach(function(cell) {
                cell.contentEditable = false;
            });

            // 隐藏保存按钮，显示编辑按钮
            $(button).siblings('.edit-btn').show();
            $(button).hide();

            // 隐藏文件上传的input
            inputElement.value = ''; // 清空选择的文件
            $(inputElement).hide();
        }


        function confirmDelete(button) {
            $(button).hide();
            $(button).siblings('.confirm-delete-btn').show();
        }

        function deleteRegion(regionId) {
            // 实现删除操作的逻辑，类似之前的代码

            // 隐藏确认删除按钮，显示删除按钮
            $('.delete-btn').show();
            $('.confirm-delete-btn').hide();
        }

        // 预览上传的图片
        function previewImage(input, regionId) {
            var row = input.parentNode.parentNode;
            var imageElement = row.cells[3].querySelector('img');
            var file = input.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imageElement.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>