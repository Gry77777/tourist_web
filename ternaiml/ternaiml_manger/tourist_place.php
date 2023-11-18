<?php
require "config.php";

$itemsPerPage = 7;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$touristsData = [];
$touristsQuery = "SELECT tourist_id, name, img FROM tourists_place LIMIT " . (($page - 1) * $itemsPerPage) . ", $itemsPerPage";
$touristsResult = $conn->query($touristsQuery);
while ($tourist = $touristsResult->fetch_assoc()) {
    $touristsData[] = [
        'tourist_id' => $tourist['tourist_id'],
        'name' => htmlspecialchars($tourist['name']),
        'img' => htmlspecialchars($tourist['img'])
    ];
}

$totalItemsQuery = "SELECT COUNT(*) AS total FROM tourists_place";
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
                <td><img src="../<?= $data['img'] ?>" alt="<?= $data['name'] ?>"></td>
                <td>
                    <button class="edit-btn" onclick="editTourist(this)" data-original-name="<?= $data['name'] ?>" data-tourist-id="<?= $data['tourist_id'] ?>">修改</button>
                    <button class="save-btn" style="display: none;" onclick="saveTourist(this)">保存</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                    <button class="confirm-delete-btn" style="display: none;" onclick="deleteTourist(<?= $data['tourist_id'] ?>)">确认删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div id="pagination">
        <button id="prevPage">上一页</button>
        <span id="currentPage"><?= $page ?></span>/<span id="totalPages"><?= ceil($totalItems / $itemsPerPage) ?></span>
        <button id="nextPage">下一页</button>
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
                const url = `tourist_place.php?page=${currentPage}`;
                window.location.href = url;
            }

            updatePageButtons();
        });
    </script>
</body>

</html>