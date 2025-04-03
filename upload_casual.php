<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

$uploader_id = $_SESSION['user_id'];
$store_name = trim($_POST['store_name']);
$url = trim($_POST['url']);
$other = trim($_POST['other']);

// 檢查是否填寫至少一項
if (empty($store_name) && empty($url) && empty($_FILES['image']['name']) && empty($other)) {
    die("至少填寫一項內容！");
}

// 圖片上傳處理
$image_path = NULL;
if (!empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . time() . "_" . $image_name; // 確保檔名不重複
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // 允許的檔案格式
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        die("只允許上傳 JPG, PNG, GIF 格式！");
    }

    // 限制檔案大小 (5MB)
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        die("檔案大小不能超過 5MB！");
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_path = $target_file;
    } else {
        die("上傳圖片失敗！");
    }
}

// 連接 MySQL
$conn = new mysqli('localhost', 'root', '', 'user_db');
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 插入資料
$stmt = $conn->prepare("INSERT INTO casual_orders (uploader_id, store_name, url, image_path, other) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $uploader_id, $store_name, $url, $image_path, $other);

if ($stmt->execute()) {
    echo "上傳成功！";
} else {
    echo "上傳失敗：" . $stmt->error;
}

$stmt->close();
$conn->close();
?>
