<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);

    // 驗證是否為該計畫提交者或管理員
    $stmt = $conn->prepare("SELECT user_id FROM travel_plans WHERE id = ?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $stmt->bind_result($plan_user_id);
    $stmt->fetch();
    $stmt->close();

    if ($plan_user_id == $user_id || $is_admin) {
        $delete_stmt = $conn->prepare("DELETE FROM travel_plans WHERE id = ?");
        $delete_stmt->bind_param("i", $plan_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }
}

header("Location: list.php");
exit;
