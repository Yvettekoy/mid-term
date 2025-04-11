<?php
session_start();
require 'db_connect.php';

// 取得使用者 ID（確認登入狀態）
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$user_id) {
    die("使用者未登入，請重新登入！");
}

// 接收表單資料
$proposal_id = $_POST['proposal_id'] ?? null;
$item_name = $_POST['item_name'] ?? null;
$quantity = $_POST['quantity'] ?? null;

// 檢查必填項是否有值
if (empty($proposal_id) || empty($item_name) || !is_numeric($quantity)) {
    die("必填項目不可為空！");
}

// 插入資料庫的 SQL 查詢
$query = "INSERT INTO item_statistics (proposal_id, user_id, item_name, quantity, created_at) 
          VALUES (?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);

// 檢查 SQL 準備是否成功
if ($stmt === false) {
    die('SQL 語句準備失敗：' . $conn->error);
}

// 綁定參數並執行
$stmt->bind_param("iisi", $proposal_id, $user_id, $item_name, $quantity);
$success = $stmt->execute();

// 檢查執行結果
if ($success) {
    echo "success";
} else {
    echo "提交失敗：" . $stmt->error;
}

// 關閉資料庫連線
$stmt->close();
$conn->close();
?>
