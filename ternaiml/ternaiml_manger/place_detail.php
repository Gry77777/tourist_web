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
            <th>Image URL</th> <!-- 新增列 -->
            <th>Actions</th>
        </tr>
        <?php foreach ($introductionData as $data) : ?>
            <tr>
                <td contenteditable="false"><?= $data['title'] ?></td>
                <td contenteditable="false"><?= $data['content'] ?></td>
                <td><img src="../<?= $data['image_url'] ?>" alt=""></td>
                <td>
                    <button class="edit-btn" onclick="editIntroduction(this)" data-original-title="<?= $data['title'] ?>" data-original-content="<?= $data['content'] ?>" data-introduction-id="<?= $data['introduction_id'] ?>">修改</button>
                    <button class="save-btn" style="display: none;" onclick="saveIntroduction(this)">保存</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                    <button class="confirm-delete-btn" style="display: none;" onclick="deleteIntroduction(<?= $data['introduction_id'] ?>)">确认删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editIntroduction(button) {
            var row = button.parentNode.parentNode;
            var editableCells = row.querySelectorAll('[contenteditable="false"]');
            editableCells.forEach(function(cell) {
                cell.contentEditable = true;
            });

            // 隐藏编辑按钮，显示保存按钮
            $(button).hide();
            $(button).siblings('.save-btn').show();
        }

        function saveIntroduction(button) {
            var row = button.parentNode.parentNode;
            var introductionId = row.cells[0].innerText;
            var newTitle = row.cells[2].innerText;
            var newContent = row.cells[3].innerText;

            // 发送保存请求
            $.ajax({
                url: 'place_detail_save.php',
                method: 'POST',
                data: {
                    introduction_id: introductionId,
                    title: newTitle,
                    content: newContent
                },
                dataType: 'json',
                success: function(response) {
                    alert(response.message);

                    // 将保存后的数据显示在表格中
                    // 这里的代码可以根据实际情况进行修改
                    row.cells[2].innerText = newTitle;
                    row.cells[3].innerText = newContent;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('保存介绍时发生错误:', errorThrown);

                    // 输出详细错误信息到控制台
                    console.log(jqXHR.responseText);

                    alert('保存介绍时发生错误，请查看控制台获取详细信息');
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
        }

        function confirmDelete(button) {
            $(button).hide();
            $(button).siblings('.confirm-delete-btn').show();
        }
        function deleteIntroduction(introductionId) {
            // 实现删除操作的逻辑，类似之前的代码
            // 隐藏确认删除按钮，显示删除按钮
            $('.delete-btn').show();
            $('.confirm-delete-btn').hide();
        }
    </script>
</body>

</html>