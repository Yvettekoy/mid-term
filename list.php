<?php
require_once 'config.php';
session_start();

// 確保使用者已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // 若未登入，轉到登入頁
    exit;
}

// 設定字符編碼為 UTF-8
$conn->set_charset("utf8");

// 查詢未逾期的出遊計畫
$sql = "SELECT * FROM travel_plans WHERE purchase_deadline >= CURDATE()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出遊計畫</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- 首頁按鈕 -->
<div class="container mt-3">
    <a href="index.php" class="btn btn-secondary">回到首頁</a>
</div>

<div class="container mt-4">
    <h2>目前出遊計畫</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $plan_id = $row['id'];
            $destination = $row['destination'];
            $departure_date = $row['departure_date'];
            $return_date = $row['return_date'];
            $purchase_deadline = $row['purchase_deadline'];
            $items = $row['items'];  // 假設是簡單的文字描述

            echo "<div class='card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title'>$destination 之旅</h5>
                        <p>出發日期: $departure_date</p>
                        <p>返回日期: $return_date</p>
                        <p>代購截止日: $purchase_deadline</p>
                        <p>項目: $items</p>
                        <form action='join_purchase.php' method='POST'>
                            <input type='hidden' name='plan_id' value='$plan_id'>
                            <button type='submit' class='btn btn-primary'>加入代購</button>
                        </form>
                    </div>
                </div>";
        }
    } else {
        echo "<p>目前無出遊計畫，敬請期待。</p>";
    }
    ?>

</div>

</body>
</html>
