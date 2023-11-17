<?php
$conn = mysqli_connect("localhost", "root", "123456");
mysqli_select_db($conn, "tourist");
mysqli_query($conn, "SET  NAMES utf8");
?>