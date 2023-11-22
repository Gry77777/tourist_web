<?php
require "config.php";

$itemsPerPage = 7;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$touristsData = [];
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchTerm = htmlspecialchars($_GET['search']);
    $searchQuery = "WHERE title LIKE '%$searchTerm%' OR introduction LIKE '%$searchTerm%'";
}
$touristsQuery = "SELECT * FROM tourist_details $searchQuery LIMIT " . (($page - 1) * $itemsPerPage) . ", $itemsPerPage";
$touristsResult = $conn->query($touristsQuery);
while ($tourist = $touristsResult->fetch_assoc()) {
    $touristsData[] = [
        'tourist_id' => $tourist['tourist_id'],
        'title' => htmlspecialchars($tourist['title']),
        'image1' => $tourist['image1'],
        'image2' => $tourist['image2'],
        'image3' => $tourist['image3'],
        'introduction' => htmlspecialchars($tourist['introduction']),
        'phone' => htmlspecialchars($tourist['phone']),
        'ticket' => htmlspecialchars($tourist['ticket']),
        'transportation' => htmlspecialchars($tourist['transportation']),
        'opening_hours' => htmlspecialchars($tourist['opening_hours'])
    ];
}

$totalItemsQuery = "SELECT COUNT(*) AS total FROM tourist_details $searchQuery";
$totalItemsResult = $conn->query($totalItemsQuery);
$totalItems = $totalItemsResult->fetch_assoc()['total'];

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tourist Details Information</title>
    <link rel="stylesheet" href="./css/tourist_detail.css">
    <script src="./js/jquery3.6.3.js"></script>
</head>
<style>
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .modal h2 {
        margin-bottom: 10px;
    }

    .modal form {
        width: 1000px;
        text-align: center;
    }

    .modal form label,
    .modal form textarea,
    .modal form input,
    .modal form button {
        width: 80%;
        margin-bottom: 10px;
    }

    .modal form textarea {
        height: 100px;
    }

    .modal form input[type="submit"],
    .modal form button {
        background-color: #4caf50;
        color: #fff;
        padding: 10px;
        border: none;
        cursor: pointer;
    }

    .modal form button {
        background-color: #f44336;
        margin-left: 10px;
    }

    .custom-button {
        background-color: #4CAF50;
        /* 绿色背景 */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
        border-radius: 8px;
        /* 圆角 */
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        /* 阴影效果 */
    }

    .custom-button:hover {
        background-color: #45a049;
        /* 悬停时的颜色 */
        color: white;
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
        /* 悬停时的阴影效果 */
    }

    #searchSection {
        margin-bottom: 20px;
    }

    #searchInput {
        padding: 10px;
        width: 200px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    #searchButton {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #searchButton:hover {
        background-color: #45a049;
    }
</style>


<body>
    <h2>景点详情管理</h2>

    <table border="1" id="yourTable">
        <tr>
            <th>id</th>
            <th>Title</th>
            <th>Image 1</th>
            <th>Image 2</th>
            <th>Image 3</th>
            <th>Introduction</th>
            <th>Phone</th>
            <th>Ticket</th>
            <th>Transportation</th>
            <th>Opening Hours</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($touristsData as $data) : ?>
            <tr>
                <td><?= $data['tourist_id'] ?></td>
                <td><?= $data['title'] ?></td>
                <td><img src="../<?= $data['image1'] ?>" alt=""></td>
                <td><img src="../<?= $data['image2'] ?>" alt=""></td>
                <td><img src="../<?= $data['image3'] ?>" alt=""></td>
                <td><?= $data['introduction'] ?></td>
                <td><?= $data['phone'] ?></td>
                <td><?= $data['ticket'] ?></td>
                <td><?= $data['transportation'] ?></td>
                <td><?= $data['opening_hours'] ?></td>
                <td>
                    <button class="edit-btn" data-original-name="<?= $data['title'] ?>" data-original-description="<?= $data['introduction'] ?>" data-original-image="<?= $data['image_url'] ?>" data-original-phone="<?= $data['phone'] ?>" data-original-ticket="<?= $data['ticket'] ?>" data-original-transportation="<?= $data['transportation'] ?>" data-original-opening-hours="<?= $data['opening_hours'] ?>" data-region-id="<?= $data['tourist_id'] ?>">
                        修改
                    </button>

                    <button class="delete-btn" onclick="confirmDelete(this)" data-original-name="<?= $data['title'] ?>" data-original-description="<?= $data['introduction'] ?>" data-original-image="<?= $data['image_url'] ?>" data-region-id="<?= $data['tourist_id'] ?>">
                        删除
                    </button>
                </td>
            </tr>

        <?php endforeach; ?>

        <button type="button" id="showAddModal" class="btn btn-primary custom-button" onclick="showAddModal()">
            添加信息
        </button>

        <div id="searchSection">
            <form method="get">
                <input type="text" name="search" id="searchInput" placeholder="输入查询条件">
                <button type="submit" id="searchButton">搜索</button>
            </form>
        </div>
    </table>

    <div id="pagination">
        <button id="prevPage">上一页</button>
        <span id="currentPage"><?= $page ?></span>/<span id="totalPages"><?= ceil($totalItems / $itemsPerPage) ?></span>
        <button id="nextPage">下一页</button>
    </div>

    <div class="modal" id="editModal">
        <h2>修改信息</h2>
        <form id="editForm" enctype="multipart/form-data">
            <input type="hidden" id="editTouristId" name="tourist_id">

            <label for="editTitle">标题:</label>
            <input type="text" id="editTitle" name="title" required>
            <br>

            <label for="editImage1">图片1路径:</label>
            <input type="file" id="editImage1" name="image1" accept="image/*">
            <br>

            <label for="editImage2">图片2路径:</label>
            <input type="file" id="editImage2" name="image2" accept="image/*">
            <br>

            <label for="editImage3">图片3路径:</label>
            <input type="file" id="editImage3" name="image3" accept="image/*">
            <br>

            <label for="editIntroduction">简介:</label>
            <textarea id="editIntroduction" name="introduction" required></textarea>
            <br>

            <label for="editPhone">联系电话:</label>
            <input type="text" id="editPhone" name="phone" required>
            <br>

            <label for="editTicket">门票信息:</label>
            <input type="text" id="editTicket" name="ticket" required>
            <br>

            <label for="editTransportation">交通信息:</label>
            <input type="text" id="editTransportation" name="transportation" required>
            <br>

            <label for="editOpeningHours">开放时间:</label>
            <input type="text" id="editOpeningHours" name="opening_hours" required>
            <br>

            <br>
            <input type="submit" value="保存修改">
            <button type="button" class="cancelBtn" onclick="closeEditModal()">取消</button>
        </form>
    </div>

    <!-- 添加的拟态框 -->

    <!-- 添加信息的表单 -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()" style=" cursor: pointer;">&times;</span>

            <h2>添加信息</h2>
            <form id="addForm" enctype="multipart/form-data">
                <div>
                    <label for="addTouristId">Tourist ID</label>
                    <input type="text" id="addTouristId" name="tourist_id" required>
                </div>
                <div>
                    <label for="addTitle">标题</label>
                    <input type="text" id="addTitle" name="title" required>
                </div>
                <div>
                    <label for="addImage1">图片1</label>
                    <input type="file" id="addImage1" name="image1">
                </div>
                <div>
                    <label for="addImage2">图片2</label>
                    <input type="file" id="addImage2" name="image2">
                </div>
                <div>
                    <label for="addImage3">图片3</label>
                    <input type="file" id="addImage3" name="image3">
                </div>
                <div>
                    <label for="addIntroduction">介绍</label>
                    <textarea id="addIntroduction" name="introduction" rows="3" required></textarea>
                </div>
                <div>
                    <label for="addPhone">电话</label>
                    <input type="text" id="addPhone" name="phone" required>
                </div>
                <div>
                    <label for="addTicket">门票</label>
                    <input type="text" id="addTicket" name="ticket" required>
                </div>
                <div>
                    <label for="addTransportation">交通方式</label>
                    <input type="text" id="addTransportation" name="transportation" required>
                </div>
                <div>
                    <label for="addOpeningHours">开放时间</label>
                    <input type="text" id="addOpeningHours" name="opening_hours" required>
                </div>
                <button type="submit">添加</button>
            </form>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            const itemsPerPage = 10;
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

            function loadPageData() {
                const url = `tourist_details.php?page=${currentPage}`;
                window.location.href = url;
            }

            updatePageButtons();

            //删除按钮
            $('.delete-btn').on('click', function() {
                // 提示用户确认删除
                if (confirm('确定要删除吗？')) {
                    const touristId = $(this).data('region-id');

                    // 发送异步请求到 delete_tourist_detail.php
                    $.ajax({
                        type: 'POST',
                        url: 'delete_tourist_detail.php',
                        data: {
                            tourist_id: touristId
                        },
                        success: function(response) {
                            if (response === 'success') {
                                // 删除成功，刷新页面或其他操作
                                location.reload();
                            } else {
                                // 删除失败，处理错误
                                console.log('Error deleting tourist');
                            }
                        },
                        error: function() {
                            console.log('Error communicating with the server');
                        }
                    });
                }
            });

            //修改按钮：
            function closeEditModal() {
                $('#editModal').hide();
            }

            $('.cancelBtn').on('click', closeEditModal);

            // 修改按钮的点击事件
            $('.edit-btn').on('click', function() {
                const originalTitle = $(this).data('original-name');
                // const originalImage1 = $(this).data('original-image1');
                // const originalImage2 = $(this).data('original-image2');
                // const originalImage3 = $(this).data('original-image3');
                const originalIntroduction = $(this).data('original-description');
                const originalPhone = $(this).data('original-phone');
                const originalTicket = $(this).data('original-ticket');
                const originalTransportation = $(this).data('original-transportation');
                const originalOpeningHours = $(this).data('original-opening-hours');
                const touristId = $(this).data('region-id');

                $('#editTitle').val(originalTitle);
                // $('#editImage1').val(originalImage1);
                // $('#editImage2').val(originalImage2);
                // $('#editImage3').val(originalImage3);
                $('#editIntroduction').val(originalIntroduction);
                $('#editPhone').val(originalPhone);
                $('#editTicket').val(originalTicket);
                $('#editTransportation').val(originalTransportation);
                $('#editOpeningHours').val(originalOpeningHours);
                $('#editModal').attr('data-tourist-id', touristId);

                $('#editModal').show();
            });

            // 保存修改按钮的点击事件
            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                const editedTitle = $('#editTitle').val();
                const editedIntroduction = $('#editIntroduction').val();
                const editedPhone = $('#editPhone').val();
                const editedTicket = $('#editTicket').val();
                const editedTransportation = $('#editTransportation').val();
                const editedOpeningHours = $('#editOpeningHours').val();
                const touristId = $('#editModal').data('tourist-id');

                // Create FormData object for handling file uploads
                const formData = new FormData();
                formData.append('tourist_id', touristId);
                formData.append('title', editedTitle);
                formData.append('introduction', editedIntroduction);
                formData.append('phone', editedPhone);
                formData.append('ticket', editedTicket);
                formData.append('transportation', editedTransportation);
                formData.append('opening_hours', editedOpeningHours);

                // Append image files to FormData if they are selected
                if ($('#editImage1')[0].files.length > 0) {
                    formData.append('image1', $('#editImage1')[0].files[0]);
                }
                if ($('#editImage2')[0].files.length > 0) {
                    formData.append('image2', $('#editImage2')[0].files[0]);
                }
                if ($('#editImage3')[0].files.length > 0) {
                    formData.append('image3', $('#editImage3')[0].files[0]);
                }

                $.ajax({
                    type: 'POST',
                    url: 'edit_tourist_detail.php',
                    data: formData,
                    processData: false,
                    contentType: false, 
                    success: function(response) {
                        if (response === 'success') {
                            location.reload();
                            console.log(response);
                        } else {
                            location.reload();
                            console.log('Error editing tourist');
                            console.log(response);
                        }
                    },
                    error: function() {
                        console.log('Error communicating with the server');
                    }
                });

                closeEditModal();
            });

        });
        //=添加ajax代码
        $("#showAddModal").click(function() {
            $("#addModal").show();
        });

        function closeAddModal() {
            $("#addModal").hide();
        }


        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            $("#addModal").hide();

            const file1 = $('#addImage1')[0].files[0];
            const file2 = $('#addImage2')[0].files[0];
            const file3 = $('#addImage3')[0].files[0];

            if (!file1 && !file2 && !file3) {
                // 如果没有文件被选择，取消提交并给出提示
                alert('请至少选择一张图片！');
                return;
            }
            alert(file1);
            const formData = new FormData(this);
            if (file1) {
                formData.append('image1', file1);
            }
            if (file2) {
                formData.append('image2', file2);
            }
            if (file3) {
                formData.append('image3', file3);
            }

            for (let pair of formData.entries()) {
                console.log(pair[0], pair[1]);
            }
            $.ajax({
                type: 'POST',
                url: 'add_tourist_detail.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    $("#addModal").hide();
                    location.reload();
                },
                error: function(error) {
                    console.log('Error saving data:', error);
                }
            });
        });
    </script>
</body>

</html>