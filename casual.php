<?php
session_start();  // 啟動 session

// 檢查是否已經登入
if (!isset($_SESSION['id'])) {
    // 如果沒有登入，跳轉回首頁
    header("Location: index.php");
    exit();
}

// 連接到資料庫
$conn = new mysqli('localhost', 'root', '', 'user_db');  // 確保資料庫名稱正確

// 檢查資料庫連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // 如果連接失敗，顯示錯誤
}

// 設定字符集為 utf8mb4
$conn->set_charset("utf8mb4");  // 確保資料庫連接使用 utf8mb4 編碼

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 確保至少填寫了一個欄位
    if (isset($_POST['store_name']) || isset($_POST['website']) || isset($_FILES['image']['name']) || isset($_POST['other'])) {
        $store_name = isset($_POST['store_name']) ? $_POST['store_name'] : '';
        $website = isset($_POST['website']) ? $_POST['website'] : '';
        $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
        $other = isset($_POST['other']) ? $_POST['other'] : '';

        // 確保圖片已經上傳到指定目錄
        if ($image && !move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image)) {
            echo "檔案上傳失敗！";
        }

        // 插入資料到資料庫
        $stmt = $conn->prepare("INSERT INTO casual_uploads (user_id, store_name, website, image, other) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['id'], $store_name, $website, $image, $other);  // 使用 $_SESSION['id']
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上傳一般團購資料</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            margin-top: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .nav {
            text-align: right;
            margin-bottom: 20px;
        }
        .nav a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            margin-right: 10px;
        }
        .nav a:hover {
            color: #4CAF50;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="nav">
            <a href="index.php">回到首頁</a>
        </div>

        <h1>上傳一般團購資料</h1>
        <form action="casual.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="store_name">店家名稱：</label>
                <input type="text" name="store_name" id="store_name" placeholder="可選填" />
            </div>

            <div class="form-group">
                <label for="website">店家網址：</label>
                <input type="text" name="website" id="website" placeholder="可選填" />
            </div>

            <div class="form-group">
                <label for="image">上傳圖片：</label>
                <input type="file" name="image" id="image" />
            </div>

            <div class="form-group">
                <label for="other">其他：</label>
                <textarea name="other" id="other" placeholder="可選填" rows="4"></textarea>
            </div>

            <button type="submit">提交資料</button>
        </form>
    </div>

</body>
</html>
