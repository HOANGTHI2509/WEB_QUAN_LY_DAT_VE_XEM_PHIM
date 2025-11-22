<?php
// BTL_MO/Handle/seats_process.php
session_start();
require_once '../functions/seats_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $screen_id = $_POST['screen_id'] ?? 0;
    $redirect_url = "../View/admin/seats.php?screen_id=" . $screen_id;
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            // 1. TẠO SƠ ĐỒ TỰ ĐỘNG (Logic mới)
            case 'generate':
                // Gọi hàm generateSeats không cần tham số hàng/cột
                $res = generateSeats($screen_id);
                
                if ($res === true) {
                    header("location: $redirect_url&success=created");
                } else {
                    throw new Exception($res);
                }
                break;

            // 2. XÓA TOÀN BỘ SƠ ĐỒ
            case 'reset':
                $res = deleteAllSeatsByScreen($screen_id);
                if ($res === true) {
                    header("location: $redirect_url&success=reset");
                } else {
                    throw new Exception($res);
                }
                break;

            // 3. XÓA 1 GHẾ
            case 'delete':
                $seat_id = $_POST['seat_id'];
                $res = deleteSeat($seat_id);
                if ($res['success']) {
                    header("location: $redirect_url&success=deleted");
                } else {
                    throw new Exception($res['message']);
                }
                break;
                
            default:
                throw new Exception("Hành động không hợp lệ");
        }
    } catch (Exception $e) {
        header("location: $redirect_url&error=" . urlencode($e->getMessage()));
    }

} 
// 4. XỬ LÝ ĐỔI LOẠI GHẾ (GET)
elseif (isset($_GET['action']) && $_GET['action'] == 'toggle') {
    if (isset($_GET['seat_id'])) {
        toggleSeatType($_GET['seat_id']);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("location: ../View/admin/screens.php");
    }
}
else {
    header("location: ../View/admin/screens.php");
}
exit;
?>