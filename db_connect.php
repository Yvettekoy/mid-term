<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "user_db";

// 建立連線
$conn = new mysqli($servername, $username, $password, $database);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定字元集為 utf8mb4
$conn->set_charset("utf8mb4");
?>
