<?php
session_start();  // 啟動 session

// 清除所有 session 資料
session_unset();
session_destroy();

// 跳轉回首頁
header("Location: index.php");
exit();
?>
