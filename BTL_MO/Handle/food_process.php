<?php
// BTL_MO/Handle/food_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/food_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../functions/admin_gate.php';

// 3. Kiểm tra xem có phải là POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Kiểm tra hành động (action) là gì?
    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        try {
            switch ($action) {
                // TRƯỜNG HỢP 1: THÊM MÓN
                case 'add':
                    $result = addFoodCombo($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/food_combos.php?success=add");
                    } else {
                        throw new Exception($result); // $result chứa chuỗi lỗi
                    }
                    break;

                // TRƯỜNG HỢP 2: CẬP NHẬT MÓN
                case 'update':
                    $result = updateFoodCombo($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/food_combos.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
                // TRƯỜNG HỢP 3: XÓA MÓN
                case 'delete':
                    if (isset($_POST['food_id'])) {
                        $result = deleteFoodCombo($_POST['food_id']);
                        if ($result['success']) {
                            header("location: ../View/admin/food_combos.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID món ăn để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            // Bắt lỗi và chuyển hướng về
            header("location: ../View/admin/food_combos.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/food_combos.php?error=No_action");
    }
} else {
    // Nếu không phải POST
    header("location: ../View/admin/food_combos.php");
}
exit;
?>