<?php
session_start();
require 'db_connect.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 檢查是否已登入
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $_SESSION['user_id'] ?? null;

if (!$isLoggedIn || !$user_id) {
    header("Location: index.php");
    exit;
}

// 查詢使用者參與的提案（item_statistics）
$query_item_statistics = "
    SELECT proposal_id, item_name, SUM(quantity) AS total_quantity
    FROM item_statistics
    WHERE user_id = ?
    GROUP BY proposal_id, item_name
    ORDER BY proposal_id DESC
";
$stmt_item_statistics = $conn->prepare($query_item_statistics);
$stmt_item_statistics->bind_param("i", $user_id);
$stmt_item_statistics->execute();
$result_item_statistics = $stmt_item_statistics->get_result();

// 查詢使用者發起的提案（casual_uploads）
$query_casual_uploads = "
    SELECT id, store_name, other, created_at
    FROM casual_uploads
    WHERE user_id = ?
    ORDER BY created_at DESC
";
$stmt_casual_uploads = $conn->prepare($query_casual_uploads);
$stmt_casual_uploads->bind_param("i", $user_id);
$stmt_casual_uploads->execute();
$result_casual_uploads = $stmt_casual_uploads->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>歷史紀錄</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .section-title {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="btn btn-secondary mb-3">返回首頁</a>

    <div class="container">
        <h2 class="section-title">🛒 我參與的團購</h2>
        <?php if ($result_item_statistics->num_rows > 0): ?>
            <ul class="list-group mb-5">
                <?php while ($row = $result_item_statistics->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong>提案 ID:</strong> <?= htmlspecialchars($row['proposal_id']) ?><br>
                        <strong>商品名稱:</strong> <?= htmlspecialchars($row['item_name']) ?><br>
                        <strong>總數量:</strong> <?= htmlspecialchars($row['total_quantity']) ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">尚未參與任何團購活動。</p>
        <?php endif; ?>

        <h2 class="section-title">📢 我發起的團購</h2>
        <?php if ($result_casual_uploads->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $result_casual_uploads->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong>店家名稱:</strong> <?= htmlspecialchars($row['store_name']) ?><br>
                        <strong>說明:</strong> <?= nl2br(htmlspecialchars($row['other'])) ?><br>
                        <small class="text-muted">建立時間: <?= htmlspecialchars($row['created_at']) ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">你還沒有發起過團購活動。</p>
        <?php endif; ?>
    </div>
</body>
</html>
