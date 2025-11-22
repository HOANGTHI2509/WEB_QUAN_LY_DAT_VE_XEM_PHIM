<?php
// BTL_MO/Handle/cancel_booking.php
session_start();
// Xóa dữ liệu đặt vé tạm thời
unset($_SESSION['booking_flow']);
// Quay về trang chủ
header("location: ../View/user/index.php");
exit;
?>