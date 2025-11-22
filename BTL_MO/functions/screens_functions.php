<?php
// BTL_MO/functions/screens_functions.php
require_once 'db_connect.php';

// Lấy tất cả phòng (kèm tên rạp)
function getAllScreens() {
    $conn = getDbConnection();
    $sql = "SELECT s.*, t.Name as TheaterName 
            FROM Screens s 
            JOIN Theaters t ON s.TheaterID = t.TheaterID 
            ORDER BY t.Name, s.Name";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Lấy thông tin 1 phòng (ĐÃ SỬA: Thêm JOIN để lấy TheaterName)
 */
function getScreenById($id) {
    $conn = getDbConnection();
    // QUAN TRỌNG: Thêm đoạn JOIN Theaters ... dưới đây
    $sql = "SELECT s.*, t.Name as TheaterName 
            FROM Screens s 
            JOIN Theaters t ON s.TheaterID = t.TheaterID 
            WHERE s.ScreenID = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addScreen($data) {
    $conn = getDbConnection();
    $name = $data['name'];
    $theater_id = $data['theater_id'];
    $capacity = $data['capacity'];

    $stmt = $conn->prepare("INSERT INTO Screens (TheaterID, Name, Capacity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $theater_id, $name, $capacity);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function updateScreen($data) {
    $conn = getDbConnection();
    $id = $data['screen_id'];
    $name = $data['name'];
    $theater_id = $data['theater_id'];
    $capacity = $data['capacity'];
    
    $stmt = $conn->prepare("UPDATE Screens SET TheaterID=?, Name=?, Capacity=? WHERE ScreenID=?");
    $stmt->bind_param("isii", $theater_id, $name, $capacity, $id);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function deleteScreen($id) {
    $conn = getDbConnection();
    // Kiểm tra xem có suất chiếu nào ở phòng này không
    $check = $conn->query("SELECT ShowtimeID FROM Showtimes WHERE ScreenID = $id LIMIT 1");
    if ($check->num_rows > 0) return ['success' => false, 'message' => 'Không thể xóa phòng chiếu đang có lịch chiếu.'];

    // Xóa ghế trước
    $conn->query("DELETE FROM Seats WHERE ScreenID = $id");
    
    $stmt = $conn->prepare("DELETE FROM Screens WHERE ScreenID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) return ['success' => true];
    return ['success' => false, 'message' => $stmt->error];
}
?>