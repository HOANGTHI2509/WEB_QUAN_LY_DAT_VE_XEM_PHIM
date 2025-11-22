<?php
// BTL_MO/functions/seattypes_functions.php
require_once 'db_connect.php';

function getAllSeatTypes() {
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM SeatTypes");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getSeatTypeById($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM SeatTypes WHERE SeatTypeID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addSeatType($data) {
    $conn = getDbConnection();
    $name = $data['name'];
    $surcharge = $data['price_surcharge'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO SeatTypes (Name, PriceSurcharge) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $surcharge);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function updateSeatType($data) {
    $conn = getDbConnection();
    $id = $data['seat_type_id'];
    $name = $data['name'];
    $surcharge = $data['price_surcharge'];
    
    $stmt = $conn->prepare("UPDATE SeatTypes SET Name = ?, PriceSurcharge = ? WHERE SeatTypeID = ?");
    $stmt->bind_param("sdi", $name, $surcharge, $id);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function deleteSeatType($id) {
    $conn = getDbConnection();
    // Kiểm tra xem có ghế nào đang dùng loại này không
    $check = $conn->query("SELECT SeatID FROM Seats WHERE SeatTypeID = $id LIMIT 1");
    if ($check->num_rows > 0) return ['success' => false, 'message' => 'Không thể xóa loại ghế đang được sử dụng trong các phòng chiếu.'];

    $stmt = $conn->prepare("DELETE FROM SeatTypes WHERE SeatTypeID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) return ['success' => true];
    return ['success' => false, 'message' => $stmt->error];
}
?>