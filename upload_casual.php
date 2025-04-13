<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php?error=not_logged_in");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上傳商品資訊</title>
</head>
<body>
    <h1>上傳商品資訊</h1>
    <form action="upload_casual.php" method="post" enctype="multipart/form-data">
        <label for="store_name">店家名稱:</label><br>
        <input type="text" id="store_name" name="store_name"><br><br>
        
        <label for="website">網址:</label><br>
        <input type="text" id="website" name="website"><br><br>
        
        <label for="image">圖片:</label><br>
        <input type="file" id="image" name="image"><br><br>
        
        <label for="other">其他資訊:</label><br>
        <textarea id="other" name="other"></textarea><br><br>
        
        <input type="submit" value="提交">
    </form>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db_connect.php'; // 確保這個檔案有建立 $conn

    $user_id = $_SESSION['user_id'];
    $store_name = trim($_POST['store_name']);
    $website = trim($_POST['website']);
    $other = trim($_POST['other']);

    // 檢查是否至少填寫一項
    if (empty($store_name) && empty($website) && empty($_FILES['image']['name']) && empty($other)) {
        die("至少填寫一項內容！");
    }

    // 處理圖片上傳
    $image_path = NULL;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // 檢查副檔名
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("只允許上傳 JPG, JPEG, PNG, GIF 格式！");
        }

        // 檔案大小限制：5MB
        if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
            die("檔案大小不能超過 5MB！");
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            die("圖片上傳失敗！");
        }
    }

    // 寫入資料庫
    $stmt = $conn->prepare("INSERT INTO casual_uploads (user_id, store_name, website, image, other) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $store_name, $website, $image_path, $other);

    if ($stmt->execute()) {
        echo "上傳成功！<a href='view_activity.php'>前往查看活動</a>";
    } else {
        echo "上傳失敗：" . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
