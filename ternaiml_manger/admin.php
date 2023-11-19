<?php
require "config.php";

$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

if (!$result) {
    die("查询失败：" . mysqli_error($conn));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Table</title>
    <script src="./js/jquery3.6.3.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        h2 {
            color: #333;
            text-align: center;
            padding: 20px;
            background-color: #4CAF50;
            /* 更深的绿色背景 */
            color: white;
            /* 白色文本 */
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-group {
            display: flex;
            gap: 5px;
        }


        .button-group button {
            padding: 0px 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <h2>后台管理员信息</h2>
    <table>
        <thead>
            <tr>
                <th>Admin ID</th>
                <th>Username</th>
                <th>Password</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Is Superadmin</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($admin = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $admin['admin_id'] . '</td>';
                echo '<td>' . $admin['username'] . '</td>';
                echo '<td>' . $admin['password'] . '</td>';
                echo '<td>' . $admin['created_at'] . '</td>';
                echo '<td>' . $admin['updated_at'] . '</td>';
                echo '<td>' . $admin['is_superadmin'] . '</td>';
                echo '<td>' . $admin['status'] . '</td>';
                echo '<td class="button-group">';

                // Check if admin_id is not 1 before displaying the buttons
                if ($admin['admin_id'] != 1) {
                    echo '<button data-action="delete" data-admin-id="' . $admin['admin_id'] . '">删除</button>';
                    echo '<button data-action="set_superadmin" data-admin-id="' . $admin['admin_id'] . '">设为管理员</button>';
                }

                echo '</td>';
                echo '</tr>';
            }
            ?>

        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('.button-group').on('click', 'button[data-action="delete"]', function() {
                const adminId = $(this).data('admin-id');
                if (confirm("确认删除这个管理员吗？")) {
                    handleAction(adminId, 'delete');
                }
            });

            $('.button-group').on('click', 'button[data-action="set_superadmin"]', function() {
                const adminId = $(this).data('admin-id');
                if (confirm("确认设置这个管理员为超级管理员吗？")) {
                    handleAction(adminId, 'set_superadmin');
                }
            });

            function handleAction(adminId, action) {
                $.ajax({
                    type: 'POST',
                    url: 'admin_table.php',
                    data: {
                        admin_id: adminId,
                        action: action
                    },
                    success: function(response) {
                        const responseData = JSON.parse(response);
                        if (responseData.status === "success") {
                            alert(responseData.message);
                            location.reload();
                        } else {
                            alert(responseData.message);
                        }
                    },
                    error: function(error) {
                        console.error('操作失败:', error);
                        alert('操作失败，请稍后重试');
                    }
                });
            }
        });
    </script>

</body>

</html>