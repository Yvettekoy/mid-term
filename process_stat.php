<?php
session_start();
require 'db_connect.php';

// 測試輸入的資料
var_dump($_POST);
exit;

// 檢查使用者是否已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo "提交失敗：請先登入";
    exit;
}

// 檢查 POST 資料是否存在
$proposal_id = $_POST['proposal_id'] ?? null;
$item_name = trim($_POST['item_name'] ?? '');
$quantity = (int) ($_POST['quantity'] ?? 0);

if (!$proposal_id || !$item_name || $quantity <= 0) {
    echo "提交失敗：請提供完整資料";
    exit;
}

// 確認活動 ID 是否存在
$check_query = "SELECT id FROM casual_uploads WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $proposal_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo "提交失敗：該活動不存在";
    exit;
}

// 插入 item_statistics
$insert_query = "INSERT INTO item_statistics (proposal_id, item_name, quantity) VALUES (?, ?, ?)";
$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("isi", $proposal_id, $item_name, $quantity);

if ($insert_stmt->execute()) {
    echo "success";
} else {
    echo "提交失敗：" . $insert_stmt->error;
}
?>
