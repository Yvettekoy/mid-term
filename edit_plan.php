<?php
require_once 'config.php';
session_start();

// 確保使用者已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'];

// 檢查是否有提供計畫 ID
if (!isset($_GET['id'])) {
    header("Location: list.php"); // 若未提供 id，轉回列表頁面
    exit;
}

$plan_id = intval($_GET['id']);

// 查詢該計畫的詳細資料
$sql = "SELECT * FROM travel_plans WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list.php"); // 若該計畫不存在，轉回列表頁面
    exit;
}

$plan = $result->fetch_assoc();
$stmt->close();

// 檢查使用者是否有權編輯該計畫
if ($plan['user_id'] !== $user_id && !$is_admin) {
    header("Location: list.php"); // 若非提交者或管理員，則無權編輯
    exit;
}

// 若表單提交，更新計畫資料
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'];
    $purchase_deadline = $_POST['purchase_deadline'];
    $items = $_POST['items'];

    $update_sql = "UPDATE travel_plans SET destination = ?, departure_date = ?, return_date = ?, purchase_deadline = ?, items = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $destination, $departure_date, $return_date, $purchase_deadline, $items, $plan_id);
    $update_stmt->execute();
    $update_stmt->close();

    // 更新成功後重定向回列表頁面
    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯出遊計畫</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-3">
    <a href="index.php" class="btn btn-secondary">回到首頁</a>
</div>

<div class="container mt-4">
    <h2>編輯出遊計畫</h2>

    <form action="edit_plan.php?id=<?php echo $plan_id; ?>" method="POST">
        <div class="mb-3">
            <label for="destination" class="form-label">目的地</label>
            <input type="text" class="form-control" id="destination" name="destination" value="<?php echo htmlspecialchars($plan['destination']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="departure_date" class="form-label">出發日期</label>
            <input type="date" class="form-control" id="departure_date" name="departure_date" value="<?php echo $plan['departure_date']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="return_date" class="form-label">返回日期</label>
            <input type="date" class="form-control" id="return_date" name="return_date" value="<?php echo $plan['return_date']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="purchase_deadline" class="form-label">代購截止日</label>
            <input type="date" class="form-control" id="purchase_deadline" name="purchase_deadline" value="<?php echo $plan['purchase_deadline']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="items" class="form-label">代購項目</label>
            <textarea class="form-control" id="items" name="items" rows="3" required><?php echo htmlspecialchars($plan['items']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">更新計畫</button>
    </form>
</div>

</body>
</html>
