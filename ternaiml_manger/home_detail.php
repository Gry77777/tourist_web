<?php
require "config.php";

$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';


// 处理翻页
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$pageSize = 6; // 每页显示的记录数

// 计算总记录数
$totalRecordsSql = "SELECT COUNT(*) as count FROM home_detail 
                    WHERE name LIKE '%$searchQuery%' 
                    OR description LIKE '%$searchQuery%' 
                    OR recommended_season LIKE '%$searchQuery%' 
                    OR suggested_stay_duration LIKE '%$searchQuery%' 
                    OR main_attractions LIKE '%$searchQuery%' 
                    OR featured_activities LIKE '%$searchQuery%'";
$totalRecordsResult = mysqli_query($conn, $totalRecordsSql);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['count'];

// 计算总页数
$totalPages = ceil($totalRecords / $pageSize);

// 计算起始位置
$start = ($currentPage - 1) * $pageSize;

// 构建查询语句，加上 LIMIT 子句实现分页
// 构建查询语句，加上 LIMIT 子句实现分页
$sql = "SELECT * FROM home_detail 
        WHERE name LIKE '%$searchQuery%' 
        OR description LIKE '%$searchQuery%' 
        OR recommended_season LIKE '%$searchQuery%' 
        OR suggested_stay_duration LIKE '%$searchQuery%' 
        OR main_attractions LIKE '%$searchQuery%' 
        OR featured_activities LIKE '%$searchQuery%'
        LIMIT $start, $pageSize";


$result = mysqli_query($conn, $sql);

// 处理查询后，重置搜索条件
$searchQuery = '';

// 处理翻页按钮的逻辑
if (isset($_POST['prevPage'])) {
    $currentPage = max(1, $currentPage - 1);
} elseif (isset($_POST['nextPage'])) {
    $currentPage = min($totalPages, $currentPage + 1);
}

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
    <form method="get" action="">
        <label for="searchQuery">查询：</label>
        <input type="text" id="searchQuery" name="searchQuery" value="<?php echo $searchQuery; ?>">
        <input type="hidden" name="page" value="<?php echo $currentPage; ?>"> <!-- 添加页码参数 -->
        <button type="submit">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>描述</th>
            <th>图片链接</th>
            <th>推荐季节</th>
            <th>建议停留时长</th>
            <th>主要景点</th>
            <th>特色活动</th>
            <th>操作</th>

        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td><img src='../" . $row['image_url'] . "' alt='Image' width='200px' height='100px'></td>";

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
                        <div id="addImagePreview1"></div>
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
        <button class="btn btn-add" onclick="showAddModal()">添加</button>
    </div>
    <!-- 添加按钮的拟态框 -->
    <div class="modal" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">添加详情</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addForm" method="post" enctype="multipart/form-data">
                        <label for="addId">ID：</label>
                        <input type="text" id="addId" name="addId" required>

                        <label for="addName">名称：</label>
                        <input type="text" id="addName" name="addName" required>

                        <label for="addDescription">描述：</label>
                        <textarea id="addDescription" name="addDescription" required></textarea>

                        <label for="addImage">图片：</label>
                        <input type="file" id="addImage" name="addImage">
                        <div id="addImagePreview"></div>

                        <label for="addRecommendedSeason">推荐季节：</label>
                        <input type="text" id="addRecommendedSeason" name="addRecommendedSeason" required>

                        <label for="addSuggestedStayDuration">建议停留时长：</label>
                        <input type="text" id="addSuggestedStayDuration" name="addSuggestedStayDuration" required>

                        <label for="addMainAttractions">主要景点：</label>
                        <input type="text" id="addMainAttractions" name="addMainAttractions" required>

                        <label for="addFeaturedActivities">特色活动：</label>
                        <input type="text" id="addFeaturedActivities" name="addFeaturedActivities" required>

                        <button type="button" class="btn btn-success1" onclick="submitAdd()">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- 翻页按钮 -->
    <div class="pagination">
        <?php
        // 显示上一页按钮
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '&searchQuery=' . urlencode($searchQuery) . '">上一页</a>';
        }
        // 显示页数
        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<a href="?page=' . $i . '&searchQuery=' . urlencode($searchQuery) . '">' . $i . '</a>';
        }
        // 显示下一页按钮
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '&searchQuery=' . urlencode($searchQuery) . '">下一页</a>';
        }
        ?>
    </div>

</body>
<script>
    var originalImagePath;
    $(document).ready(function() {
        $("#addImage").change(function() {
            var input = this;
            var url = URL.createObjectURL(input.files[0]);
            $("#addImagePreview").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
        });
        $("#modifyImage").change(function() {
            var input = this;
            var url = URL.createObjectURL(input.files[0]);
            $("#addImagePreview1").html("<img src='" + url + "' alt='Preview Image' style='max-width: 100px; max-height: 100px;'>");
        });
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
                    location.reload();
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

        // 获取上传的新图片文件
        var newImageInput = $('#modifyImage')[0];
        if (newImageInput.files.length > 0) {
            // 有新图片上传，将新图片添加到FormData中
            modifiedData.append('modifyImage', newImageInput.files[0]);
        }

        // 将原图片路径添加到FormData中
        modifiedData.append('originalImagePath', originalImagePath);

        console.log("Modified Data:", modifiedData);

        // 通过Ajax发送修改后的数据
        $.ajax({
            url: 'home_detail_edit.php', // 替换成更新数据的后端接口
            type: 'POST',
            data: modifiedData,
            contentType: false,
            processData: false,
            success: function(response) {
                location.reload();
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

    //添加商品的ajax代码
    function showAddModal() {
        // 清空添加表单
        $('#addForm')[0].reset();
        // 显示拟态框
        $('#addModal').show();
    }

    $('.close').on('click', function() {
        // 关闭拟态框
        $('#addModal').hide();
    });
    $('.btn-success1').on('click', function() {
        submitAdd();
    });
    // 提交添加数据的表单
    function submitAdd() {
        // 获取添加数据的表单数据
        var addData = new FormData();
        addData.append('id', $('#addId').val());
        addData.append('name', $('#addName').val());
        addData.append('description', $('#addDescription').val());
        addData.append('recommended_season', $('#addRecommendedSeason').val());
        addData.append('suggested_stay_duration', $('#addSuggestedStayDuration').val());
        addData.append('main_attractions', $('#addMainAttractions').val());
        addData.append('featured_activities', $('#addFeaturedActivities').val());

        // 获取上传的新图片文件
        var newImageInput = $('#addImage')[0];
        if (newImageInput.files.length > 0) {
            // 有新图片上传，将新图片添加到FormData中
            addData.append('addImage', newImageInput.files[0]);
        }

        // 通过Ajax发送添加数据的请求
        $.ajax({
            url: 'home_detail_add.php', // 替换成处理添加数据的后端接口
            type: 'POST',
            data: addData,
            contentType: false,
            processData: false,
            success: function(response) {
                // 处理成功添加的情况
                location.reload();
                console.log(response);
                // 刷新页面或更新UI等操作
                $('#addModal').hide();

            },
            error: function(error) {
                alert(error); 
                location.reload();
                // 处理添加失败的情况

                console.log(error);
            }
        });
    }
</script>



</html>