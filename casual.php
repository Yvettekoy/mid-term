<?php
session_start();  // 啟動 session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 檢查是否已經登入
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['store_name']) || isset($_POST['website']) || isset($_FILES['image']['name']) || isset($_POST['other'])) {
        $store_name = $_POST['store_name'] ?? '';
        $website = $_POST['website'] ?? '';
        $other = $_POST['other'] ?? '';
        $image = '';

        // 若未上傳圖片則使用預設圖片
        if (isset($_FILES['image']) && $_FILES['image']['name']) {
            $image = basename($_FILES['image']['name']);
            $uploadPath = 'uploads/' . $image;

            // 確保 uploads 資料夾存在
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                echo "檔案上傳失敗！";
            }
        } else {
            $image = '95720'; // 預設圖片名稱
        }

        // 將資料插入資料庫
        $stmt = $conn->prepare("INSERT INTO casual_uploads (user_id, store_name, website, image, other) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['user_id'], $store_name, $website, $image, $other);
        $stmt->execute();
        $stmt->close();
        echo "資料已成功上傳！";
    } else {
        echo "請至少填寫一個欄位！";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>上傳一般團購資料</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: black;
        }
        .navbar a {
            color: white !important;
        }
        .navbar-nav .btn {
            margin-left: 5px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- 黑色選單 -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- 回到首頁按鈕（置左） -->
            <a class="btn btn-outline-light me-2" href="index.php">回到首頁</a>

            <!-- 漢堡選單 -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- 中間與右側選單 -->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <!-- 中間：查看活動 -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="view_activity.php">查看活動</a>
                    </li>
                </ul>

                <!-- 右側：登出按鈕 -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="logout.php">登出</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card shadow">
            <div class="card-body">
                <!-- 表單標題 -->
                <h1 class="card-title text-center mb-4">上傳一般團購資料</h1>

                <!-- 表單內容 -->
                <form action="casual.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="store_name" class="form-label">店家名稱：</label>
                        <input type="text" name="store_name" id="store_name" class="form-control" placeholder="可選填">
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">店家網址：</label>
                        <input type="text" name="website" id="website" class="form-control" placeholder="可選填">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">上傳圖片：</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label for="other" class="form-label">其他：</label>
                        <textarea name="other" id="other" rows="4" class="form-control" placeholder="可選填"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">提交資料</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
