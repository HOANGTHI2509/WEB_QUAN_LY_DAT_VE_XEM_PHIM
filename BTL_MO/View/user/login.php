<?php
// BTL_MO/View/user/login.php

session_start();

// Nếu đã đăng nhập, chuyển thẳng về trang chủ
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("location: index.php"); 
    exit;
}

// Biến này dùng để header biết cần link CSS nào
$page_css = "auth.css"; 
// (Không cần include header.php vì đây là trang layout đặc biệt)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - CinemaHub</title>
    
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <a href="index.php" class="logo" style="text-decoration: none;">
                     <svg width="48" height="48" ...></svg>
                     <span>CinemaHub</span>
                </a>
                <h1>Đăng nhập</h1>
                <p>Chào mừng bạn quay trở lại</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    if ($_GET['error'] == 'empty') echo 'Vui lòng nhập đủ email và mật khẩu!';
                    elseif ($_GET['error'] == 'invalid') echo 'Email hoặc mật khẩu không chính xác!';
                    elseif ($_GET['error'] == 'unauthorized') echo 'Bạn phải là Admin để vào trang đó!';
                    elseif ($_GET['error'] == 'required') echo 'Bạn cần đăng nhập để thực hiện chức năng này!';
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'registered'): ?>
                <div class="alert alert-success">
                    Đăng ký tài khoản thành công! Vui lòng đăng nhập.
                </div>
            <?php endif; ?>

            <form id="loginForm" class="auth-form" action="../../Handle/login_process.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                           Hiện/Ẩn
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <span>Đăng nhập</span>
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="register.php" class="link">Đăng ký ngay</a></p>
            </div>
        </div>
        <div class="auth-image">
            </div>
    </div>
    
    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>