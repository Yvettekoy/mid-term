<?php
$host = 'localhost'; // MySQL 伺服器地址
$user = 'root';      // MySQL 使用者名稱
$password = '';      // MySQL 密碼，如果沒有設定密碼，請留空
$database = 'user_db'; // 資料庫名稱

// 建立資料庫連線
$conn = new mysqli($host, $user, $password, $database);

// 檢查是否連線成功
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}
?>
