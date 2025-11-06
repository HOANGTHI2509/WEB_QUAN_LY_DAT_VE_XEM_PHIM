<?php
// BTL_MO/View/user/register.php
session_start();

// Nếu đã đăng nhập, chuyển thẳng về trang chủ
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("location: index.php"); 
    exit;
}

$page_css = "auth.css"; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - CinemaHub</title>
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
                <h1>Đăng ký</h1>
                <p>Tạo tài khoản để trải nghiệm CinemaHub</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php
                    if ($_GET['error'] == 'empty') echo 'Vui lòng nhập đầy đủ thông tin!';
                    elseif ($_GET['error'] == 'email_invalid') echo 'Email không hợp lệ!';
                    elseif ($_GET['error'] == 'password_short') echo 'Mật khẩu phải có ít nhất 6 ký tự!';
                    elseif ($_GET['error'] == 'password_mismatch') echo 'Xác nhận mật khẩu không khớp!';
                    elseif ($_GET['error'] == 'email_exists') echo 'Email này đã được sử dụng.';
                    else echo 'Đã xảy ra lỗi. Vui lòng thử lại.';
                    ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" class="auth-form" action="../../Handle/register_process.php" method="POST">
                <div class="form-group">
                    <label for="name">Họ và tên</label>
                    <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" placeholder="0912345678" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="••••••••" required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">Hiện/Ẩn</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Xác nhận mật khẩu</label>
                    <div class="password-input">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">Hiện/Ẩn</button>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <span>Đăng ký</span>
                </button>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="login.php" class="link">Đăng nhập ngay</a></p> 
            </div>
        </div>
        <div class="auth-image"></div>
    </div>
    
    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>