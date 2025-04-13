<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_POST['option'])) {
    header("Location: login.php");
    exit();
}

$upload_id = $_POST['upload_id'];
$option = $_POST['option'];
$is_admin = $_SESSION['is_admin'] ?? false;

if (!$is_admin) {
    die("只有管理員可以修改選項！");
}

// 儲存選項
$stmt = $conn->prepare("INSERT INTO options (upload_id, option) VALUES (?, ?)");
$stmt->bind_param("is", $upload_id, $option);
$stmt->execute();

header("Location: view_activity.php?upload_id=" . $upload_id);
exit();
?>
