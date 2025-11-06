<?php
// BTL_MO/Handle/logout_process.php
// [Dựa trên file bạn đã cung cấp]

// 1. Luôn bắt đầu session
session_start();

// 2. Xóa tất cả các biến session
$_SESSION = array();

// 3. Hủy session
session_destroy();

// 4. Chuyển hướng người dùng về trang đăng nhập (trong View/user)
header("location: ../View/user/login.php?status=logout");
exit;
?>