<?php
require "config.php";

$itemsPerPage = 7;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$touristsData = [];
$touristsQuery = "SELECT * FROM tourist_details LIMIT " . (($page - 1) * $itemsPerPage) . ", $itemsPerPage";
$touristsResult = $conn->query($touristsQuery);
while ($tourist = $touristsResult->fetch_assoc()) {
    $touristsData[] = [
        'detail_id' => $tourist['detail_id'],
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

$totalItemsQuery = "SELECT COUNT(*) AS total FROM tourist_details";
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

<body>
    <h2>景点详情管理</h2>
    <table border="1" id="yourTable">
        <tr>
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
                    <button class="edit-btn" onclick="editRegion(this)" data-original-name="<?= $data['name'] ?>" data-original-description="<?= $data['description'] ?>" data-original-image="<?= $data['image_url'] ?>" data-region-id="<?= $data['region_id'] ?>">修改</button>
                    <button class="save-btn" style="display: none;" onclick="saveRegion(this)">保存</button>
                    <button class="delete-btn" onclick="confirmDelete(this)">删除</button>
                    <button class="confirm-delete-btn" style="display: none;" onclick="deleteRegion(<?= $data['region_id'] ?>)">确认删除</button>
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
                const url = `tourist_details.php?page=${currentPage}`;
                window.location.href = url;
            }

            updatePageButtons();
        });
    </script>
</body>

</html>