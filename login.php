<?php
session_start();  // 啟動 Session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['phone'], $_POST['password'])) {
        $phone = $_POST['phone'];
        $password = $_POST['password'];

        // 連接資料庫
        $conn = new mysqli('localhost', 'root', '', 'user_db');

        if ($conn->connect_error) {
            die("資料庫連線失敗：" . $conn->connect_error);
        }

        // 查詢用戶資料
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ? AND password = ?");
        $stmt->bind_param("ss", $phone, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // 登入成功
            $user = $result->fetch_assoc();
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];       // ✅ 統一變數名稱
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];

            header("Location: index.php");
            exit();
        } else {
            // 登入失敗（帳密錯誤）
            echo "❌ 手機或密碼錯誤！";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "⚠️ 請填寫手機和密碼！";
    }
}
?>
