<?php
session_start();
require 'db_connect.php';

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $_SESSION['id'] ?? null;
$user_name = $_SESSION['name'] ?? '';

if (!$user_id) {
    die("錯誤：用戶 ID 為空，無法提交！");
}

$departure_date = date('Y-m-d');
$return_date = date('Y-m-d', strtotime('+2 days', strtotime($departure_date)));
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>出遊計劃</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<!-- 導覽列 -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">首頁</a>
    </div>
</nav>

<div class="container mt-4">
    <h2>出遊計劃</h2>
    <form method="POST" action="abroad.php">
        <div class="mb-3">
            <label for="destination" class="form-label">出遊地點</label>
            <input type="text" class="form-control" id="destination" name="destination" required>
        </div>

        <div class="mb-3 row">
            <div class="col-md-6">
                <label for="departure_date" class="form-label">啟程日</label>
                <input type="date" class="form-control" id="departure_date" name="departure_date" value="<?= $departure_date ?>" required>
            </div>
            <div class="col-md-6">
                <label for="return_date" class="form-label">返程日</label>
                <input type="date" class="form-control" id="return_date" name="return_date" value="<?= $return_date ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="purchase_deadline" class="form-label">代購截止日</label>
            <input type="date" class="form-control" id="purchase_deadline" name="purchase_deadline"
                   placeholder="如未選擇，將自動設為返程日前兩天">
        </div>

        <div class="mb-3">
            <label for="items" class="form-label">代購項目</label><br>
            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-success me-2" id="select-all">全選</button>
                <button type="button" class="btn btn-sm btn-danger" id="deselect-all">取消全選</button>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="食品類" id="food">
                <label class="form-check-label" for="food">食品類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="藥品及美妝類" id="medicine">
                <label class="form-check-label" for="medicine">藥品及美妝類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="衣著類" id="clothes">
                <label class="form-check-label" for="clothes">衣著類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="飾品類" id="accessories">
                <label class="form-check-label" for="accessories">飾品類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="家居類" id="home">
                <label class="form-check-label" for="home">家居類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="娛樂類" id="entertainment">
                <label class="form-check-label" for="entertainment">娛樂類</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="限量商品" id="limited">
                <label class="form-check-label" for="limited">限量商品</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="奢侈品" id="luxury">
                <label class="form-check-label" for="luxury">奢侈品</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="items[]" value="其他" id="other">
                <label class="form-check-label" for="other">其他</label>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="thanksModal" tabindex="-1" aria-labelledby="thanksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">感恩的心</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
            </div>
            <div class="modal-body">
                謝謝 <?= $user_name ?>，大家愛你!!!!!!!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">確認</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#select-all').click(function () {
            $('input[type="checkbox"]').prop('checked', true);
        });
        $('#deselect-all').click(function () {
            $('input[type="checkbox"]').prop('checked', false);
        });
    });
</script>

</body>
</html>

<?php
// 表單處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'];
    $purchase_deadline = $_POST['purchase_deadline'];

    if (empty($purchase_deadline)) {
        $purchase_deadline = date('Y-m-d', strtotime('-2 days', strtotime($return_date)));
    }

    $items = isset($_POST['items']) ? implode(", ", $_POST['items']) : '';

    $stmt = $conn->prepare("INSERT INTO travel_plans (user_id, destination, departure_date, return_date, purchase_deadline, items) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $destination, $departure_date, $return_date, $purchase_deadline, $items);

    if ($stmt->execute()) {
        echo "<script>var myModal = new bootstrap.Modal(document.getElementById('thanksModal')); myModal.show();</script>";
    } else {
        echo "提交失敗：" . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
