<?php
require "config.php";
$sql = "SELECT * FROM home_detail";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Details</title>
    <link rel="stylesheet" href="./css/home_detail.css">
    <script src="./js/jquery3.6.3.js"></script>

</head>

<body>
    <h2>首页详情管理</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image URL</th>
            <th>Recommended Season</th>
            <th>Suggested Stay Duration</th>
            <th>Main Attractions</th>
            <th>Featured Activities</th>
            <th>Action</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>" . $row['image_url'] . "</td>";
            echo "<td>" . $row['recommended_season'] . "</td>";
            echo "<td>" . $row['suggested_stay_duration'] . "</td>";
            echo "<td>" . $row['main_attractions'] . "</td>";
            echo "<td>" . $row['featured_activities'] . "</td>";
            echo "<td>
                    <button class='btn btn-modify' data-id='" . $row['id'] . "'>修改</button>
                    <button class='btn btn-delete' data-id='" . $row['id'] . "'>删除</button>
                </td>";
            echo "</tr>";
        }
        ?>
    </table>
    <!-- 拟态框 -->

    <div class="modal" id="modifyModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">修改详情</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="modifyForm" method="post" enctype="multipart/form-data">
                        <label for="modifyName">Name:</label>
                        <input type="text" id="modifyName" name="modifyName" required>

                        <label for="modifyDescription">Description:</label>
                        <textarea id="modifyDescription" name="modifyDescription" required></textarea>

                        <label for="modifyImage">Image:</label>
                        <input type="file" id="modifyImage" name="modifyImage">

                        <label for="modifyRecommendedSeason">Recommended Season:</label>
                        <input type="text" id="modifyRecommendedSeason" name="modifyRecommendedSeason" required>

                        <label for="modifySuggestedStayDuration">Suggested Stay Duration:</label>
                        <input type="text" id="modifySuggestedStayDuration" name="modifySuggestedStayDuration" required>

                        <label for="modifyMainAttractions">Main Attractions:</label>
                        <input type="text" id="modifyMainAttractions" name="modifyMainAttractions" required>

                        <label for="modifyFeaturedActivities">Featured Activities:</label>
                        <input type="text" id="modifyFeaturedActivities" name="modifyFeaturedActivities" required>

                        <input type="hidden" id="modifyID" name="modifyID">

                        <button type="button" class="btn btn-success" onclick="submitModification()">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="btn-container">
        <button class="btn btn-add">添加</button>
    </div>
</body>
<script>
    var originalImagePath;
    $(document).ready(function() {
        $('.btn-delete').on('click', function() {
            var id = $(this).data('id');
            deleteRecord(id);
        });

        $('.btn-modify').on('click', function() {
            var id = $(this).closest('tr').find('td:eq(0)').text();
            var name = $(this).closest('tr').find('td:eq(1)').text();
            var description = $(this).closest('tr').find('td:eq(2)').text();
            var imageUrl = $(this).closest('tr').find('td:eq(3)').text();
            var recommendedSeason = $(this).closest('tr').find('td:eq(4)').text();
            var suggestedStayDuration = $(this).closest('tr').find('td:eq(5)').text();
            var mainAttractions = $(this).closest('tr').find('td:eq(6)').text();
            var featuredActivities = $(this).closest('tr').find('td:eq(7)').text();
            originalImagePath = $(this).closest('tr').find('td:eq(3)').text();
            // 将获取的数据填充到拟态框中
            $('#modifyID').val(id);
            $('#modifyName').val(name);
            $('#modifyDescription').val(description);
            $('#modifyRecommendedSeason').val(recommendedSeason);
            $('#modifySuggestedStayDuration').val(suggestedStayDuration);
            $('#modifyMainAttractions').val(mainAttractions);
            $('#modifyFeaturedActivities').val(featuredActivities);

            // 显示拟态框
            $('#modifyModal').show();
        });
        $('.close').on('click', function() {
            // 关闭拟态框
            $('#modifyModal').hide();
        });
        $('.btn-success').on('click', function() {
            submitModification();
        });
    });

    function deleteRecord(id) {
        if (confirm("确定删除吗？")) {
            $.ajax({
                url: 'home_detail_delete.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    // 处理成功删除的情况
                    console.log(response);
                    // 刷新页面或更新UI等操作
                },
                error: function(error) {
                    // 处理删除失败的情况
                    console.log(error);
                }
            });
        }
    }

    function submitModification() {
        // 获取修改后的数据
        var modifiedData = new FormData();
        modifiedData.append('id', $('#modifyID').val());
        modifiedData.append('name', $('#modifyName').val());
        modifiedData.append('description', $('#modifyDescription').val());
        modifiedData.append('recommended_season', $('#modifyRecommendedSeason').val());
        modifiedData.append('suggested_stay_duration', $('#modifySuggestedStayDuration').val());
        modifiedData.append('main_attractions', $('#modifyMainAttractions').val());
        modifiedData.append('featured_activities', $('#modifyFeaturedActivities').val());
        alert(modifiedData);
        for (let pair of modifiedData.entries()) {
            console.log(pair[0] + ', ' + pair[1]);
        }

        // 获取上传的新图片文件
        var newImageInput = $('#modifyImage')[0]; // 假设上传图片的input的id为modifyImage
        if (newImageInput.files.length > 0) {
            // 有新图片上传，将新图片添加到FormData中
            modifiedData.append('modifyImage', newImageInput.files[0]);
        }

        // 将原图片路径添加到FormData中
        modifiedData.append('originalImagePath', originalImagePath);

        console.log("Modified Data:", modifiedData);
        console.log(modifiedData);
        // 通过Ajax发送修改后的数据
        $.ajax({
            url: 'home_detail_edit.php', // 替换成更新数据的后端接口
            type: 'POST',
            data: modifiedData,
            contentType: false,
            processData: false,
            success: function(response) {
                // 处理成功更新的情况
                console.log(response);
                // 刷新页面或更新UI等操作
                $('#modifyModal').hide();
            },
            error: function(error) {
                // 处理更新失败的情况
                console.log(error);
            }
        });
    }
</script>



</html>