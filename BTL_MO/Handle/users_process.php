<?php
// BTL_MO/Handle/users_process.php

session_start();
require_once '../functions/users_functions.php';

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
                // THÊM USER
                case 'add':
                    $result = addUser($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/users.php?success=add");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // CẬP NHẬT USER
                case 'update':
                    $result = updateUser($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/users.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
                // XÓA USER
                case 'delete':
                    if (isset($_POST['UserID'])) {
                        $result = deleteUser($_POST['UserID']);
                        if ($result['success']) {
                            header("location: ../View/admin/users.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID người dùng để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            header("location: ../View/admin/users.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/users.php?error=No_action");
    }
} else {
    header("location: ../View/admin/users.php");
}
exit;
?>