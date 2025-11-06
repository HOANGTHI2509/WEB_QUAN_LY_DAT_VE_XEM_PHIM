<?php
// BTL_MO/Handle/theaters_process.php

session_start();
require_once '../functions/theaters_functions.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    die("Bạn không có quyền truy cập.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        try {
            switch ($action) {
                // THÊM RẠP
                case 'add':
                    $result = addTheater($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/theaters.php?success=add");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // CẬP NHẬT RẠP
                case 'update':
                    $result = updateTheater($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/theaters.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
                // XÓA RẠP
                case 'delete':
                    if (isset($_POST['TheaterID'])) {
                        $result = deleteTheater($_POST['TheaterID']);
                        if ($result['success']) {
                            header("location: ../View/admin/theaters.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID rạp để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            header("location: ../View/admin/theaters.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/theaters.php?error=No_action");
    }
} else {
    header("location: ../View/admin/theaters.php");
}
exit;
?>