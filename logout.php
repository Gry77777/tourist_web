<?php
// 处理用户退出登录逻辑
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // 重定向回登录页面
exit;
?>
