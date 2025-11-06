<?php
// BTL_MO/Handle/movies_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/movies_functions.php';

// 2. Kiểm tra quyền Admin (vì chỉ Admin mới được làm việc này)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    die("Bạn không có quyền truy cập.");
}

// 3. Kiểm tra xem có phải là POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Kiểm tra hành động (action) là gì?
    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        try {
            switch ($action) {
                // TRƯỜNG HỢP 1: THÊM PHIM MỚI
                case 'add':
                    // $_POST chứa tất cả dữ liệu từ form
                    $result = addMovie($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/movies.php?success=add");
                    } else {
                        throw new Exception($result); // $result chứa chuỗi lỗi
                    }
                    break;

                // TRƯỜNG HỢP 2: CẬP NHẬT PHIM
                case 'update':
                    $result = updateMovie($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/movies.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
                // TRƯỜNG HỢP 3: XÓA PHIM
                case 'delete':
                    if (isset($_POST['movie_id'])) {
                        $result = deleteMovie($_POST['movie_id']);
                        if ($result['success']) {
                            header("location: ../View/admin/movies.php?success=delete");
                        } else {
                            throw new Exception($result['message']);
                        }
                    } else {
                        throw new Exception("Thiếu ID phim để xóa.");
                    }
                    break;
                
                default:
                    throw new Exception("Hành động không hợp lệ.");
            }
        } catch (Exception $e) {
            // Bắt lỗi và chuyển hướng về trang trước đó
            header("location: ../View/admin/movies.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/movies.php?error=No_action");
    }
} else {
    // Nếu không phải POST
    header("location: ../View/admin/movies.php");
}
exit;
?>