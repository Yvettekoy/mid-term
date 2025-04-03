<?php
session_start();

// 檢查使用者是否已登入
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "logged_in";
} else {
    echo "not_logged_in";
}
?>
