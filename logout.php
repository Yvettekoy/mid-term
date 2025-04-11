<?php
session_start();
session_destroy(); // 清除會話
header('Location: index.php'); // 登出後返回首頁
exit();
?>
