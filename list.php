<?php
require_once 'config.php';
session_start();

// 確保使用者已登入
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // 若未登入，轉到登入頁
    exit;
}

// 設定字元編碼為 UTF-8
$conn->set_charset("utf8");

// 檢查 user_id 和 is_admin 是否設置，若未設置則給予預設值
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

// 除錯輸出
// echo "User ID: $user_id, Admin: $is_admin";

$sql = "SELECT travel_plans.*, users.name AS submitter_name FROM travel_plans
        JOIN users ON travel_plans.user_id = users.id
        WHERE purchase_deadline >= CURDATE()";
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

<!-- 顯示使用者姓名 -->
<div class="container mt-3">
    <p>您好，<?php echo $_SESSION['name']; ?>，歡迎來到出遊計畫頁面！</p>
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
            $submitter_name = $row['submitter_name']; // 提交者名稱

            echo "<div class='card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title'>$destination 之旅</h5>
                        <p>出發日期: $departure_date</p>
                        <p>返回日期: $return_date</p>
                        <p>代購截止日: $purchase_deadline</p>
                        <p>項目: $items</p>
                        <p>提交者: $submitter_name</p>";

            // 顯示編輯和刪除按鈕
            if ($is_admin || $row['user_id'] == $user_id) {
                // 顯示編輯和刪除按鈕
                echo "<a href='edit_plan.php?id=$plan_id' class='btn btn-warning'>編輯</a>
                      <a href='delete_plan.php?id=$plan_id' class='btn btn-danger'>刪除</a>";
            } else {
                echo "<p>您無權修改此計畫。</p>";
            }

            echo "<form action='join_purchase.php' method='POST'>
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
