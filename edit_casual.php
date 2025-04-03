<?php
session_start();
require 'db_connect.php'; // 確保有連接資料庫

// 檢查是否登入
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    die("請先登入！");
}

// 獲取目前登入的使用者 ID
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? false; // 預設不是管理員

// 確保有提供要編輯的 ID
if (!isset($_GET['id'])) {
    die("缺少必要參數！");
}

$upload_id = $_GET['id'];

// 查詢該筆上傳的資料
$stmt = $conn->prepare("SELECT * FROM casual_uploads WHERE id = ?");
$stmt->bind_param("i", $upload_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("找不到該筆資料！");
}

// 檢查權限（只有上傳者或管理員可以編輯）
if ($data['user_id'] != $user_id && !$is_admin) {
    die("你沒有權限修改這筆資料！");
}

// 如果是 POST 請求，執行更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_name = $_POST['store_name'] ?? null;
    $url = $_POST['url'] ?? null;
    $other = $_POST['other'] ?? null;
    
    // 圖片處理
    if (!empty($_FILES['image']['name'])) {
        $image_path = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    } else {
        $image_path = $data['image_path']; // 如果沒上傳新圖片，就保持舊的
    }

    // 更新資料庫
    $stmt = $conn->prepare("UPDATE casual_uploads SET store_name=?, url=?, image_path=?, other=? WHERE id=?");
    $stmt->bind_param("ssssi", $store_name, $url, $image_path, $other, $upload_id);
    $stmt->execute();

    echo "更新成功！";
}

// 顯示編輯表單
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯一般團購</title>
</head>
<body>
    <h2>編輯一般團購</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>店家名稱：</label>
        <input type="text" name="store_name" value="<?= htmlspecialchars($data['store_name']) ?>"><br>

        <label>網址：</label>
        <input type="text" name="url" value="<?= htmlspecialchars($data['url']) ?>"><br>

        <label>圖片上傳：</label>
        <input type="file" name="image"><br>
        <img src="<?= htmlspecialchars($data['image_path']) ?>" width="100"><br>

        <label>其他：</label>
        <textarea name="other"><?= htmlspecialchars($data['other']) ?></textarea><br>

        <button type="submit">更新</button>
    </form>
</body>
</html>
