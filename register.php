<?php
session_start();  // 啟用 Session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 確認所有欄位都有提交
    if (isset($_POST['name'], $_POST['phone'], $_POST['birthday'], $_POST['password'])) {
        // 取得表單資料
        $name = $_POST['name'];
        $birthday = $_POST['birthday'];  // 假設生日是民國年格式（例如：0860831）
        $phone = $_POST['phone'];
        $password = $_POST['password'];  // 確保密碼欄位存在

        // 轉換民國年為西元年
        $year = substr($birthday, 0, 3) + 1911;  // 民國年 + 1911 = 西元年
        $month_day = substr($birthday, 3);  // 取得月日
        $birthday_western = $year . '-' . substr($month_day, 0, 2) . '-' . substr($month_day, 2); // 西元日期格式 YYYY-MM-DD

        // 連接資料庫
        $conn = new mysqli('localhost', 'root', '', 'user_db');  // 確保使用正確的資料庫名稱

        // 檢查資料庫連接是否成功
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);  // 如果連接失敗，顯示錯誤
        }

        // 檢查手機是否已經註冊過
        $stmt_check = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt_check->bind_param("s", $phone);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            die("這個手機號碼已經註冊過了！");
        }

        // 插入資料
        $stmt = $conn->prepare("INSERT INTO users (name, phone, birthday, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $birthday_western, $password);  // 傳入西元年生日和密碼
        $stmt->execute();

        // 註冊成功，開始自動登入
        $_SESSION['logged_in'] = true;
        $_SESSION['phone'] = $phone; // 使用手機號碼作為帳號登入
        $_SESSION['name'] = $name; // 儲存用戶名稱

        // 註冊成功後自動跳轉回首頁
        header("Location: index.php");
        exit();

        $stmt->close();
        $conn->close();
    } else {
        echo "請填寫所有欄位！";
    }
}
?>
