<?php
$host = 'localhost';
$db   = 'user_db';
$user = 'root';
$pass = '';

// 建立資料庫連線
$conn = new mysqli($host, $user, $pass, $db);

// 檢查連線是否成功
if ($conn->connect_error) {
    die('連線失敗: ' . $conn->connect_error);
}

// 設定編碼
$conn->set_charset("utf8mb4");
?>
