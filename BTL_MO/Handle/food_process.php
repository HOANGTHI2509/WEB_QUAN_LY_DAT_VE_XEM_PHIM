<?php
// BTL_MO/Handle/food_process.php

session_start();
// 1. Gọi file functions
require_once '../functions/food_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../functions/admin_gate.php';

// --- HÀM HỖ TRỢ UPLOAD ẢNH ---
function uploadFoodImage($file) {
    // Kiểm tra xem có file được upload không và không có lỗi
    if (!isset($file['name']) || $file['error'] != 0) {
        return null;
    }

    // Đường dẫn thư mục lưu ảnh
    $target_dir = "../assets/uploads/food/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Lấy đuôi file (jpg, png...)
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Kiểm tra định dạng cho phép
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowed_types)) {
        throw new Exception("Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF, WEBP).");
    }

    // Tạo tên file mới để tránh trùng lặp: food_TIMESTAMP_RANDOM.ext
    $new_name = "food_" . time() . "_" . rand(1000, 9999) . "." . $imageFileType;
    $target_file = $target_dir . $new_name;

    // Di chuyển file từ thư mục tạm sang thư mục đích
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Trả về đường dẫn tương đối để lưu vào DB (bỏ ../ ở đầu)
        return "assets/uploads/food/" . $new_name;
    } else {
        throw new Exception("Có lỗi khi tải file lên server.");
    }
}
// -----------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $result = null;

        try {
            // XỬ LÝ UPLOAD ẢNH CHUNG CHO CẢ ADD VÀ UPDATE
            $image_path = null;
            
            // Nếu có chọn file mới
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $image_path = uploadFoodImage($_FILES['image_file']);
            } 
            // Nếu không chọn file mới nhưng có ảnh cũ (trường hợp Update)
            elseif (isset($_POST['current_image']) && !empty($_POST['current_image'])) {
                $image_path = $_POST['current_image'];
            }

            // Gán đường dẫn ảnh vào mảng $_POST để hàm functions sử dụng
            // (Hàm addFoodCombo/updateFoodCombo trong food_functions.php đang lấy data['image_url'])
            $_POST['image_url'] = $image_path;

            switch ($action) {
                // TRƯỜNG HỢP 1: THÊM MÓN
                case 'add':
                    // Kiểm tra nếu thêm mới mà không có ảnh (nếu bắt buộc)
                    if (empty($image_path)) {
                        // Nếu muốn ảnh là bắt buộc thì throw Exception ở đây
                        // throw new Exception("Vui lòng chọn hình ảnh.");
                    }
                    
                    $result = addFoodCombo($_POST); 
                    if ($result === true) {
                        header("location: ../View/admin/food_combos.php?success=add");
                    } else {
                        throw new Exception($result);
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
                        // (Tùy chọn) Bạn có thể lấy đường dẫn ảnh từ DB và xóa file vật lý tại đây nếu muốn
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
    header("location: ../View/admin/food_combos.php");
}
exit;
?>