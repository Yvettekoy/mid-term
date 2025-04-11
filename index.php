<?php
session_start();  // 啟動 Session，檢查是否有登入狀態
header('Content-Type: text/html; charset=UTF-8');
// 檢查是否已經登入
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首頁</title>
    <!-- 引入 Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar {
            background-color: #333;
        }
        .navbar a {
            color: #fff;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
        .alert-icon {
            font-size: 50px;
        }
        .card-deck {
            margin-top: 30px;
        }
        .notification-icons {
            font-size: 25px;
            color: #fff;
            margin-right: 20px;
        }
        .notification-icons:hover {
            cursor: pointer;
        }
        /* 按鈕間隔 */
        .modal-btn {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- 工作列 -->
<nav class="navbar">
    <a href="#">財務部團購登記系統</a>
    <?php if ($isLoggedIn): ?>
        <div class="d-flex align-items-center ml-auto">
            <!-- 通知鈴鐺 -->
            <i class="bi bi-bell notification-icons" data-bs-toggle="tooltip" title="通知提醒"></i>

            <!-- 對話泡泡圖示 -->
            <i class="bi bi-chat-left-text notification-icons" data-bs-toggle="tooltip" title="對話通知"></i>

            <a href="logout.php">登出 (<?php echo $_SESSION['name']; ?>)</a>
        </div>
    <?php else: ?>
        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">登入</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">註冊</a>
    <?php endif; ?>
</nav>

<!-- 登入提醒 (只在登入成功後顯示) -->
<?php if ($isLoggedIn): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center mt-3">
        <div>
            <i class="bi bi-check-circle alert-icon"></i>
            <strong>登入成功!</strong> 歡迎回來，<?php echo $_SESSION['name']; ?>！
        </div>
    </div>
<?php endif; ?>

<!-- 功能選單 (卡片顯示) -->
<div class="container">
    <div class="row card-deck">
        <!-- 查看正在進行活動 -->
        <div class="col-md-4">
            <div class="card">
            <img src="uploads/activity.jpg" class="card-img-top" alt="活動圖片" style="width: 30%; height: auto;">
                <div class="card-body">
                    <h5 class="card-title">查看正在進行活動</h5>
                    <p class="card-text">查看目前所有正在進行中的活動，了解活動狀況。</p>
                    <?php if ($isLoggedIn): ?>
                        <a href="view_activity.php" class="btn btn-primary">查看活動</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">請先登入</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 上傳提案 -->
        <div class="col-md-4">
            <div class="card">
            <img src="uploads/upload.png" class="card-img-top" alt="活動圖片" style="width: 30%; height: auto;">
                <div class="card-body">
                    <h5 class="card-title">上傳提案</h5>
                    <p class="card-text">提交您的提案，與我們分享您的創意。</p>
                    <!-- 按鈕點擊時顯示選項 modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proposalModal">
                        上傳提案
                    </button>
                </div>
            </div>
        </div>

        <!-- 花費歷史 -->
        <div class="col-md-4">
            <div class="card">
            <img src="uploads/cost.png" class="card-img-top" alt="活動圖片" style="width: 30%; height: auto;">
                <div class="card-body">
                    <h5 class="card-title">已參與活動</h5>
                    <p class="card-text">查看您有加入的團購及數量。</p>
                    <a href="history.php" class="btn btn-primary">查看歷史</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-4 mx-auto mt-4" style="max-width: 300px;">
    <div class="card">
        <img src="uploads/countdown.png" class="card-img-top" alt="限時活動圖片" style="width: 60%; height: auto; margin: auto;">
        <div class="card-body text-center">
            <h5 class="card-title">限時活動</h5>
            <p class="card-text">搶先參加期間限定的團購活動，不容錯過！</p>
            <a href="list.php" class="btn btn-primary">查看限時活動</a>
        </div>
    </div>
</div>


<!-- 上傳提案選擇 modal -->
<div class="modal fade" id="proposalModal" tabindex="-1" aria-labelledby="proposalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proposalModalLabel">選擇提案類型</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>請選擇您要上傳的提案類型：</p>
                <div class="d-flex justify-content-between">
                    <!-- 當按下一般團購時跳轉到 casual.php -->
                    <button type="button" class="btn btn-outline-primary modal-btn" onclick="window.location.href='casual.php';" data-bs-dismiss="modal">一般團購</button>
                    <button type="button" class="btn btn-outline-primary modal-btn" onclick="window.location.href='abroad.php';" data-bs-dismiss="modal">出遊版本</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 登入 Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">登入</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="loginPhone" class="form-label">手機</label>
                        <input type="text" class="form-control" id="loginPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">密碼</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-custom">登入</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 註冊 Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">註冊</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="registerName" class="form-label">名稱</label>
                        <input type="text" class="form-control" id="registerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPhone" class="form-label">手機</label>
                        <input type="text" class="form-control" id="registerPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerBirthday" class="form-label">生日 (民國年)</label>
                        <input type="text" class="form-control" id="registerBirthday" name="birthday" required placeholder="例如：0860831">
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">密碼</label>
                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-custom">註冊</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
