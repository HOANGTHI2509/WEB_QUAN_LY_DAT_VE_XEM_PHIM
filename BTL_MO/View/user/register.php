<?php
// BTL_MO/View/user/register.php
session_start();

// Nếu đã đăng nhập, chuyển thẳng về trang chủ
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("location: index.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - CinemaHub</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">

    <style>
        .auth-message {
            border: 1px solid; padding: 15px; border-radius: 8px;
            margin-bottom: 20px; font-weight: 500; text-align: center;
        }
        .auth-error {
            background: rgba(229, 9, 20, 0.1);
            border-color: rgba(229, 9, 20, 0.3);
            color: #e50914;
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <a href="index.php" class="logo" style="text-decoration: none;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2.5 16.5l19-9m-19 0l19 9M7 5v14m10-14v14"></path>
                    </svg>
                    <span>CinemaHub</span>
                </a>
                <h1>Đăng ký</h1>
                <p>Tạo tài khoản để trải nghiệm Cinema</p>
            </div>

            <?php
            // (ĐOẠN HIỂN THỊ THÔNG BÁO LỖI - Giữ nguyên như code của bạn)
            if (isset($_GET['error'])) {
                $message = '';
                if ($_GET['error'] == 'empty') {
                    $message = 'Vui lòng nhập đầy đủ thông tin!';
                } elseif ($_GET['error'] == 'email_invalid') {
                    $message = 'Email không hợp lệ!';
                } elseif ($_GET['error'] == 'password_short') {
                    $message = 'Mật khẩu phải có ít nhất 6 ký tự!';
                } elseif ($_GET['error'] == 'password_mismatch') {
                    $message = 'Xác nhận mật khẩu không khớp!';
                } elseif ($_GET['error'] == 'email_exists') {
                    $message = 'Email này đã được sử dụng. Vui lòng chọn email khác.';
                } elseif ($_GET['error'] == 'unknown') {
                    $message = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
                
                if ($message) {
                    echo '<div class="auth-message auth-error">' . htmlspecialchars($message) . '</div>';
                }
            }
            ?>

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
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                           Hiện/Ẩn
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Xác nhận mật khẩu</label>
                    <div class="password-input">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                            Hiện/Ẩn
                        </button>
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