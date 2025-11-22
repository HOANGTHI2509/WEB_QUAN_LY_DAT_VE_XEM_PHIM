<?php
// BTL_MO/Handle/users_process.php

session_start();
require_once '../functions/users_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Kiểm tra action
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        try {
            switch ($action) {
                // 1. THÊM NGƯỜI DÙNG
                case 'add':
                    $result = addUser($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/users.php?success=add");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // 2. CẬP NHẬT NGƯỜI DÙNG
                case 'update':
                    $result = updateUser($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/users.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                // 3. XÓA NGƯỜI DÙNG
                case 'delete':
                    if (isset($_POST['UserID'])) {
                        $result = deleteUser($_POST['UserID']);
                        if ($result['success']) {
                            header("location: ../View/admin/users.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID người dùng.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            header("location: ../View/admin/users.php?error=" . urlencode($e->getMessage()));
        }
    } else {
        header("location: ../View/admin/users.php");
    }
} else {
    header("location: ../View/admin/users.php");
}
exit;
?>