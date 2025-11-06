<?php
// BTL_MO/Handle/bookings_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/bookings_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        try {
            switch ($action) {
                // TRƯỜSNG HỢP 1: CẬP NHẬT TRẠNG THÁI
                case 'update_status':
                    if (isset($_POST['booking_id']) && isset($_POST['new_status'])) {
                        $result = updateBookingStatus($_POST['booking_id'], $_POST['new_status']);
                        if ($result === true) {
                            header("location: ../View/admin/bookings.php?success=update");
                        } else {
                            throw new Exception("Cập nhật trạng thái thất bại.");
                        }
                    } else {
                        throw new Exception("Thiếu ID đơn hàng hoặc trạng thái mới.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            header("location: ../View/admin/bookings.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/bookings.php?error=No_action");
    }
} else {
    header("location: ../View/admin/bookings.php");
}
exit;
?>