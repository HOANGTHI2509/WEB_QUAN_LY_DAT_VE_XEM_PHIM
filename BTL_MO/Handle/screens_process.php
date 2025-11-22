<?php
// BTL_MO/Handle/screens_process.php
session_start();
require_once '../functions/screens_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add':
                $res = addScreen($_POST);
                if ($res === true) header("location: ../View/admin/screens.php?success=add");
                else throw new Exception($res);
                break;
            case 'update':
                $res = updateScreen($_POST);
                if ($res === true) header("location: ../View/admin/screens.php?success=update");
                else throw new Exception($res);
                break;
            case 'delete':
                $res = deleteScreen($_POST['screen_id']);
                if ($res['success']) header("location: ../View/admin/screens.php?success=delete");
                else throw new Exception($res['message']);
                break;
        }
    } catch (Exception $e) {
        header("location: ../View/admin/screens.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../View/admin/screens.php");
}
// ... (Các hàm add, update, delete giữ nguyên) ...

/**
 * Hàm hỗ trợ API: Lấy danh sách phòng theo ID Rạp
 * (Dùng cho dropdown ở trang Thêm suất chiếu)
 */
function getScreensByTheater($theater_id) {
    $conn = getDbConnection();
    $screens = [];
    
    $sql = "SELECT ScreenID, Name, Capacity FROM Screens WHERE TheaterID = ? ORDER BY Name";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $theater_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $screens = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
    $conn->close();
    
    return $screens;
}
?>
?>