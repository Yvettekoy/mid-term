<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_POST['quantity'])) {
    header("Location: login.php");
    exit();
}

$upload_id = $_POST['upload_id'];
$quantity = $_POST['quantity'];
$user_id = $_SESSION['user_id'];

// 檢查是否為該用戶或管理員
$stmt = $conn->prepare("SELECT user_id FROM casual_orders WHERE upload_id = ? AND user_id = ?");
$stmt->bind_param("ii", $upload_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0 && !$_SESSION['is_admin']) {
    die("只有管理員或該使用者可以更改數量！");
}

// 更新數量
$stmt = $conn->prepare("INSERT INTO casual_orders (upload_id, user_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
$stmt->bind_param("iiii", $upload_id, $user_id, $quantity, $quantity);
$stmt->execute();

header("Location: view_activity.php?upload_id=" . $upload_id);
exit();
?>
