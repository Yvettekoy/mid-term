<?php
session_start();
require 'db_connect.php';

// 檢查使用者是否已登入
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// 查詢所有活動資料
$query = "SELECT * FROM casual_uploads ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活動列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">首頁</a>
        <div class="d-flex">
            <?php if ($isLoggedIn): ?>
                <a href="logout.php" class="btn btn-outline-light">登出 (<?php echo $_SESSION['name']; ?>)</a>
            <?php else: ?>
                <a href="#" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">登入</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>正在進行的活動</h2>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['store_name']); ?></h5>
                        <ul class="list-group">
                            <?php
                            $activity_id = $row['id'];
                            $stat_query = "SELECT item_name, SUM(quantity) as total FROM item_statistics WHERE proposal_id = ? GROUP BY item_name";
                            $stat_stmt = $conn->prepare($stat_query);
                            $stat_stmt->bind_param("i", $activity_id);
                            $stat_stmt->execute();
                            $stat_result = $stat_stmt->get_result();
                            while ($stat_row = $stat_result->fetch_assoc()):
                            ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($stat_row['item_name']); ?>：<?php echo $stat_row['total']; ?> 個
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <div class="mt-3">
                            <input type="text" class="form-control mb-2 item-name-<?php echo $activity_id; ?>" placeholder="輸入品項">
                            <input type="number" class="form-control mb-2 item-qty-<?php echo $activity_id; ?>" placeholder="數量" min="1">
                            <button class="btn btn-success add-item-btn" data-activity="<?php echo $activity_id; ?>">加入</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".add-item-btn").click(function () {
        var activityId = $(this).data("activity");
        var itemName = $(".item-name-" + activityId).val().trim();
        var itemQty = $(".item-qty-" + activityId).val().trim();

        if (itemName === "" || itemQty === "" || itemQty <= 0) {
            alert("請輸入有效的品項名稱和數量！");
            return;
        }

        $.post("process_stat.php", { 
            proposal_id: activityId,  // 修正參數名稱，確保與後端一致
            item_name: itemName,  
            quantity: itemQty 
        }, function (response) {
            alert(response);
            if (response === "success") {
                location.reload();
            } else {
                alert("提交失敗：" + response);
            }
        });
    });
});
</script>
</body>
</html>