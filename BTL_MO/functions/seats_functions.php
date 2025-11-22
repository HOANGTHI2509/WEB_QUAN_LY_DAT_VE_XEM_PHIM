<?php
// BTL_MO/functions/seats_functions.php
require_once 'db_connect.php';

/**
 * Lấy danh sách ghế của một phòng chiếu cụ thể
 */
function getSeatsByScreen($screen_id) {
    $conn = getDbConnection();
    // Lấy thông tin ghế + tên loại ghế + giá phụ thu
    // (Đã xóa st.Color để tránh lỗi nếu DB chưa có)
    $sql = "SELECT s.*, st.Name as SeatTypeName, st.PriceSurcharge 
            FROM Seats s
            JOIN SeatTypes st ON s.SeatTypeID = st.SeatTypeID
            WHERE s.ScreenID = ?
            ORDER BY LENGTH(s.RowName), s.RowName, s.SeatNumber";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Lỗi SQL: " . $conn->error);
    
    $stmt->bind_param("i", $screen_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy danh sách loại ghế
 */
function getSeatTypes() {
    $conn = getDbConnection();
    return $conn->query("SELECT * FROM SeatTypes ORDER BY PriceSurcharge ASC")->fetch_all(MYSQLI_ASSOC);
}

/**
 * SINH SƠ ĐỒ GHẾ TỰ ĐỘNG (DỰA TRÊN SỨC CHỨA)
 * - Tự tính số hàng/cột
 * - Dừng khi đủ số lượng ghế (Capacity)
 */
function generateSeats($screen_id) {
    $conn = getDbConnection();
    
    // 1. Lấy sức chứa hiện tại của phòng
    $screen = $conn->query("SELECT Capacity FROM Screens WHERE ScreenID = $screen_id")->fetch_assoc();
    if (!$screen) return "Phòng chiếu không tồn tại.";
    $capacity = (int)$screen['Capacity']; 
    
    if ($capacity <= 0) return "Sức chứa phòng phải lớn hơn 0. Vui lòng sửa thông tin phòng trước.";

    // 2. Tính toán Layout
    // Mặc định: 10 ghế/hàng. Phòng lớn (>100 ghế) -> 12-16 ghế/hàng
    $cols_per_row = 10; 
    if ($capacity > 100) $cols_per_row = 12;
    if ($capacity > 200) $cols_per_row = 16;

    // Tính tổng số hàng cần thiết (làm tròn lên)
    $total_rows = ceil($capacity / $cols_per_row); 
    
    if ($total_rows > 26) return "Sức chứa quá lớn, vượt quá giới hạn 26 hàng chữ cái (A-Z).";

    // 3. Lấy ID loại ghế từ DB (Chuẩn xác)
    $types = $conn->query("SELECT SeatTypeID, PriceSurcharge FROM SeatTypes ORDER BY PriceSurcharge ASC")->fetch_all(MYSQLI_ASSOC);
    
    // Nếu không có loại ghế nào, báo lỗi
    if (empty($types)) return "Chưa có loại ghế nào trong hệ thống.";

    // Loại rẻ nhất là Thường, loại đắt hơn là VIP
    $type_normal = $types[0]['SeatTypeID'];
    // Nếu có loại thứ 2 thì lấy, không thì lấy loại thường luôn
    $type_vip = isset($types[1]) ? $types[1]['SeatTypeID'] : $type_normal;

    $alphabet = range('A', 'Z'); 
    
    $conn->begin_transaction();
    try {
        // Xóa ghế cũ trước khi tạo mới
        $conn->query("DELETE FROM Seats WHERE ScreenID = $screen_id");

        $sql = "INSERT INTO Seats (ScreenID, SeatTypeID, RowName, SeatNumber) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $seats_created = 0; // Biến đếm số ghế đã tạo

        for ($r = 0; $r < $total_rows; $r++) {
            $rowName = $alphabet[$r];
            
            // LOGIC: 3 hàng đầu là Thường, còn lại là VIP
            $current_type = ($r < 3) ? $type_normal : $type_vip;

            for ($c = 1; $c <= $cols_per_row; $c++) {
                // QUAN TRỌNG: Nếu đã tạo đủ số ghế bằng sức chứa thì dừng lại ngay
                if ($seats_created >= $capacity) {
                    break; 
                }

                $stmt->bind_param("iisi", $screen_id, $current_type, $rowName, $c);
                $stmt->execute();
                $seats_created++;
            }
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return "Lỗi: " . $e->getMessage();
    }
}

/**
 * Xóa toàn bộ sơ đồ
 */
function deleteAllSeatsByScreen($screen_id) {
    $conn = getDbConnection();
    $check = $conn->query("SELECT TicketID FROM Tickets t JOIN Seats s ON t.SeatID = s.SeatID WHERE s.ScreenID = $screen_id LIMIT 1");
    if ($check && $check->num_rows > 0) return "Không thể xóa sơ đồ vì phòng này đã có vé bán ra.";

    $conn->query("DELETE FROM Seats WHERE ScreenID = $screen_id");
    return true;
}

/**
 * Đổi loại ghế nhanh
 */
function toggleSeatType($seat_id) {
    $conn = getDbConnection();
    $current = $conn->query("SELECT SeatTypeID FROM Seats WHERE SeatID = $seat_id")->fetch_assoc();
    if (!$current) return false;

    $types = $conn->query("SELECT SeatTypeID FROM SeatTypes ORDER BY SeatTypeID ASC")->fetch_all(MYSQLI_ASSOC);
    $ids = array_column($types, 'SeatTypeID');
    
    $key = array_search($current['SeatTypeID'], $ids);
    $next_id = $ids[($key + 1) % count($ids)]; 

    $conn->query("UPDATE Seats SET SeatTypeID = $next_id WHERE SeatID = $seat_id");
    return true;
}

/**
 * Xóa 1 ghế lẻ
 */
function deleteSeat($seat_id) {
    $conn = getDbConnection();
    $check = $conn->query("SELECT TicketID FROM Tickets WHERE SeatID = $seat_id LIMIT 1");
    if ($check && $check->num_rows > 0) return ['success' => false, 'message' => 'Ghế này đã có vé, không thể xóa'];

    $conn->query("DELETE FROM Seats WHERE SeatID = $seat_id");
    return ['success' => true];
}
?>