<?php
// BTL_MO/Handle/promotions_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/promotions_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        try {
            switch ($action) {
                // TRƯỜNG HỢP 1: THÊM KHUYẾN MÃI
                case 'add':
                    $result = addPromotion($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/promotions.php?success=add");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // TRƯỜNG HỢP 2: CẬP NHẬT KHUYẾN MÃI
                case 'update':
                    $result = updatePromotion($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/promotions.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
                // TRƯỜNG HỢP 3: XÓA KHUYẾN MÃI
                case 'delete':
                    if (isset($_POST['promotion_id'])) {
                        $result = deletePromotion($_POST['promotion_id']);
                        if ($result['success']) {
                            header("location: ../View/admin/promotions.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID khuyến mãi để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            // Bắt lỗi và chuyển hướng về
            header("location: ../View/admin/promotions.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/promotions.php?error=No_action");
    }
} else {
    header("location: ../View/admin/promotions.php");
}
exit;
?>