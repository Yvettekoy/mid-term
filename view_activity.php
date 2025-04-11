<?php
session_start();
require 'db_connect.php';

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $_SESSION['user_id'] ?? null;

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">首頁</a>
        <div class="d-flex">
            <?php if ($isLoggedIn): ?>
                <a href="logout.php" class="btn btn-outline-light">登出 (<?php echo $_SESSION['name']; ?>)</a>
            <?php else: ?>
                <a href="#" class="btn btn-outline-light">登入</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>正在進行的活動</h2>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): 
            $activity_id = $row['id'];

            $item_query = "SELECT DISTINCT item_name FROM item_statistics WHERE proposal_id = ?";
            $item_stmt = $conn->prepare($item_query);
            $item_stmt->bind_param("i", $activity_id);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();

            $item_names = [];
            while ($item_row = $item_result->fetch_assoc()) {
                $item_names[] = $item_row['item_name'];
            }

            // 每個項目的使用者數量資料
            $chart_data = [];
            foreach ($item_names as $item_name) {
                $user_query = "SELECT u.name, SUM(i.quantity) as total FROM item_statistics i 
                               JOIN users u ON i.user_id = u.id
                               WHERE i.proposal_id = ? AND i.item_name = ? 
                               GROUP BY i.user_id";
                $user_stmt = $conn->prepare($user_query);
                $user_stmt->bind_param("is", $activity_id, $item_name);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $chart_data[$item_name] = [];
                while ($ur = $user_result->fetch_assoc()) {
                    $chart_data[$item_name][] = [
                        'user_name' => $ur['name'],
                        'total' => $ur['total']
                    ];
                }
            }
        ?>
            <div class="col-md-6">
                <div class="card mb-4">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['store_name']); ?></h5>
                        <ul class="list-group">
                            <?php foreach ($item_names as $item_name): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($item_name); ?>
                                    <button class="btn btn-sm btn-outline-primary add-more-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addModal"
                                            data-activity-id="<?php echo $activity_id; ?>"
                                            data-item-name="<?php echo htmlspecialchars($item_name); ?>">
                                        ＋
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-3">
                            <input type="text" class="form-control mb-2 item-name-<?php echo $activity_id; ?>" placeholder="輸入新項目">
                            <input type="number" class="form-control mb-2 item-qty-<?php echo $activity_id; ?>" placeholder="數量">
                            <button class="btn btn-success add-item-btn" data-activity="<?php echo $activity_id; ?>">新增項目</button>
                        </div>

                        <div class="mt-4">
                            <canvas id="chart-<?php echo $activity_id; ?>"></canvas>
                        </div>

                        <script>
                        const ctx<?php echo $activity_id; ?> = document.getElementById('chart-<?php echo $activity_id; ?>');

                        new Chart(ctx<?php echo $activity_id; ?>, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode(array_keys($chart_data)); ?>,
                                datasets: [
                                    <?php 
                                    $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#fb7185'];
                                    $colorIndex = 0;
                                    $user_colors = [];
                                    $user_names_used = [];

                                    // 找出所有 user_names
                                    foreach ($chart_data as $item_entries) {
                                        foreach ($item_entries as $entry) {
                                            $user_names_used[$entry['user_name']] = true;
                                        }
                                    }

                                    $user_names = array_keys($user_names_used);
                                    foreach ($user_names as $username) {
                                        $user_colors[$username] = $colors[$colorIndex % count($colors)];
                                        $colorIndex++;
                                    }

                                    foreach ($user_names as $username): 
                                    ?>
                                    {
                                        label: " <?php echo $username; ?>",
                                        data: [
                                            <?php
                                            foreach ($chart_data as $entries) {
                                                $found = false;
                                                foreach ($entries as $entry) {
                                                    if ($entry['user_name'] == $username) {
                                                        echo $entry['total'] . ",";
                                                        $found = true;
                                                        break;
                                                    }
                                                }
                                                if (!$found) echo "0,";
                                            }
                                            ?>
                                        ],
                                        backgroundColor: '<?php echo $user_colors[$username]; ?>'
                                    },
                                    <?php endforeach; ?>
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        </script>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addItemForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">輸入數量</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="activity_id" id="modalActivityId">
          <input type="hidden" name="item_name" id="modalItemName">
          <div class="mb-3">
            <label for="modalQuantity" class="form-label">數量</label>
            <input type="number" class="form-control" name="quantity" id="modalQuantity" min="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">送出</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".add-item-btn").click(function () {
        const activityId = $(this).data("activity");
        const itemName = $(".item-name-" + activityId).val().trim();
        const itemQty = $(".item-qty-" + activityId).val().trim() || 0;  // 默認為0

        if (!itemName) {
            alert("請輸入有效的品項名稱！");
            return;
        }

        $.post("process_stat.php", {
            proposal_id: activityId,
            item_name: itemName,
            quantity: itemQty,
            user_id: <?php echo json_encode($user_id); ?>
        }, function (response) {
            if (response === "success") {
                location.reload();
            } else {
                alert("提交失敗：" + response);
            }
        });
    });

    $(".add-more-btn").click(function () {
        const itemName = $(this).data("item-name");
        const activityId = $(this).data("activity-id");

        $("#modalItemName").val(itemName);
        $("#modalActivityId").val(activityId);
        $("#modalQuantity").val('');  // 清空數量
    });

    $("#addItemForm").submit(function (e) {
        e.preventDefault();

        $.post("process_stat.php", {
            proposal_id: $("#modalActivityId").val(),
            item_name: $("#modalItemName").val(),
            quantity: $("#modalQuantity").val(),
            user_id: <?php echo json_encode($user_id); ?>
        }, function (response) {
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