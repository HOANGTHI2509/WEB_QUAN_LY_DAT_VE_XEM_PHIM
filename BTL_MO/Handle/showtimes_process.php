<?php
// BTL_MO/Handle/showtimes_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/showtimes_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;
        // Lấy ngày để chuyển hướng (nếu có)
        $date = $_POST['show_date'] ?? date('Y-m-d');

        try {
            switch ($action) {
                // TRƯỜNG HỢP 1: THÊM SUẤT CHIẾU
                case 'add':
                    $result = addShowtime($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/showtimes.php?success=add&filter_date=" . $date);
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // TRƯỜNG HỢP 2: CẬP NHẬT SUẤT CHIẾU (MỚI)
                case 'update':
                    $result = updateShowtime($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/showtimes.php?success=update&filter_date=" . $date);
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // TRƯỜNG HỢP 3: XÓA SUẤT CHIẾU (MỚI)
                case 'delete':
                    if (isset($_POST['showtime_id'])) {
                        $result = deleteShowtime($_POST['showtime_id']);
                        if ($result['success']) {
                            // Lấy ngày lọc từ URL (nếu có) để quay về đúng ngày đó
                            $filter_date = $_POST['filter_date'] ?? date('Y-m-d');
                            header("location: ../View/admin/showtimes.php?success=delete&filter_date=" . $filter_date);
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID suất chiếu để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            header("location: ../View/admin/showtimes.php?error=" . urlencode($e->getMessage()) . "&filter_date=" . $date);
        }

    } else {
        header("location: ../View/admin/showtimes.php?error=No_action");
    }
} else {
    header("location: ../View/admin/showtimes.php");
}
exit;

?>