<?php
// BTL_MO/functions/admin_gate.php
// [File này dựa trên file bạn đã cung cấp, đã sửa đường dẫn]

// Luôn bắt đầu session (nếu chưa có)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * File này được gọi từ các trang trong /View/admin/
 * Ví dụ: /View/admin/dashboard.php
 * Đường dẫn đúng để quay về trang login là: ../user/login.php
 */

// Kiểm tra 2 điều:
// 1. Người dùng đã đăng nhập CHƯA? (isset($_SESSION['user_id']))
// 2. Người dùng có phải là Admin KHÔNG? ($_SESSION['role'] == 'Admin')
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    
    // Nếu không phải admin, "đuổi" họ về trang đăng nhập
    // Đường dẫn chính xác: từ /View/admin/ -> /View/user/login.php
    header('Location: ../user/login.php?error=unauthorized');
    exit(); // Dừng chạy code ngay lập tức
}

// Nếu đúng là admin, code của trang (ví dụ dashboard.php) sẽ được phép chạy tiếp
?>