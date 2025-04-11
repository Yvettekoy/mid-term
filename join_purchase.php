<?php
session_start();
require_once 'config.php';

// 檢查是否登入
if (!isset($_SESSION['user_id'])) {
    die('請先登入才能加入代購。');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);
    $user_id = $_SESSION['user_id'];

    // 檢查是否已經加入該計畫的代購（避免重複）
    $check_sql = "SELECT * FROM casual_orders WHERE user_id = ? AND travel_plan_id = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $plan_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "你已經加入這個代購了。";
    } else {
        // 插入代購紀錄
        $insert_sql = "INSERT INTO casual_orders (user_id, travel_plan_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $mysqli->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $plan_id);

        if ($stmt->execute()) {
            echo "成功加入代購！<a href='list.php'>返回活動列表</a>";
        } else {
            echo "加入失敗：" . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
} else {
    echo "請求不正確。";
}

$mysqli->close();
?>
