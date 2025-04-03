<?php
session_start();  // 啟動 Session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 確認有提供手機和密碼
    if (isset($_POST['phone'], $_POST['password'])) {
        $phone = $_POST['phone'];
        $password = $_POST['password'];

        // 連接到資料庫
        $conn = new mysqli('localhost', 'root', '', 'user_db');  // 確保資料庫名稱正確

        // 檢查資料庫連接
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);  // 如果連接失敗，顯示錯誤
        }

        // 查詢用戶資料
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ? AND password = ?");
        $stmt->bind_param("ss", $phone, $password);  // 用手機和密碼查詢
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // 登入成功
            $user = $result->fetch_assoc();
            $_SESSION['logged_in'] = true;
            $_SESSION['phone'] = $user['phone'];  // 記錄使用者的手機作為帳號
            $_SESSION['name'] = $user['name'];  // 記錄用戶名稱
            $_SESSION['id'] = $user['id'];  // 記錄使用者的 ID

            // 登入成功後跳轉回首頁
            header("Location: index.php");
            exit();
        } else {
            // 登入失敗
            echo "手機或密碼錯誤！";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "請填寫手機和密碼！";
    }
}
?>
