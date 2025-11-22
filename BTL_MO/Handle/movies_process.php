<?php
// BTL_MO/Handle/movies_process.php

session_start();
require_once '../functions/movies_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        
        $action = $_POST['action'];
        $result = null;

        // XỬ LÝ UPLOAD ẢNH
        $uploaded_poster_path = null;
        if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] == 0) {
            $uploaded_poster_path = uploadMoviePoster($_FILES['poster_file']);
        }

        // Gán đường dẫn ảnh vào $_POST
        if ($uploaded_poster_path) {
            $_POST['poster_file_path'] = $uploaded_poster_path;
        }

        try {
            switch ($action) {
                case 'add':
                    // $_POST đã chứa 'new_actors' từ form
                    $result = addMovie($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/movies.php?success=add");
                    } else {
                        throw new Exception($result);
                    }
                    break;

                case 'update':
                    $result = updateMovie($_POST);
                    if ($result === true) {
                        header("location: ../View/admin/movies.php?success=update");
                    } else {
                        throw new Exception($result);
                    }
                    break;
                
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
            header("location: ../View/admin/movies.php?error=" . urlencode($e->getMessage()));
        }

    } else {
        header("location: ../View/admin/movies.php?error=No_action");
    }
} else {
    header("location: ../View/admin/movies.php");
}
exit;
?>