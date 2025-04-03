<?php
// 檢查授權碼
$auth_code = $_POST['auth_code'];

// 定義正確的授權碼
$correct_code = "11204513";

if ($auth_code === $correct_code) {
    // 如果授權碼正確，顯示註冊表單
    echo "<h2>授權碼正確，請繼續註冊</h2>";
    echo '<form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">名稱</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="birthday">生日</label>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
            <div class="form-group">
                <label for="phone">電話</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">註冊</button>
          </form>';
} else {
    // 如果授權碼錯誤，跳轉回首頁並顯示錯誤訊息
    header("Location: index.php?error=auth_code"); // 使用參數來告訴首頁授權碼錯誤
    exit(); // 確保後續程式不繼續執行
}
?>
