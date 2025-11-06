<?php
// BTL_MO/functions/auth.php
// [File này dựa trên file bạn đã cung cấp]

// Luôn bắt đầu session (nếu chưa có)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * File này được gọi từ các trang trong /View/user/
 * Ví dụ: /View/user/lich-su-dat-ve.php
 * Đường dẫn đúng để quay về trang login là: login.php (vì cùng thư mục)
 */

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    
    // Đẩy về trang login.php
    header("location: login.php?error=required");
    exit; 
}

// Nếu đã đăng nhập, code sẽ được chạy tiếp
?>