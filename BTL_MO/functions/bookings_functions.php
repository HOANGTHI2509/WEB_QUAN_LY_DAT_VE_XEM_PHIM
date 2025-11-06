<?php
// BTL_MO/functions/bookings_functions.php

require_once 'db_connect.php';

/**
 * Lấy tất cả đơn hàng (join với nhiều bảng)
 * @return array Danh sách đơn hàng
 */
function getAllBookings() {
    $conn = getDbConnection();
    $bookings = [];

    // Câu lệnh SQL phức tạp để lấy thông tin từ 5 bảng
    $sql = "SELECT 
                b.BookingID, b.TotalAmount, b.BookingTime, b.PaymentStatus,
                u.FullName AS CustomerName,
                m.Title AS MovieTitle,
                st.StartTime,
                GROUP_CONCAT(CONCAT(s.RowName, s.SeatNumber) ORDER BY s.RowName, s.SeatNumber SEPARATOR ', ') AS Seats
            FROM Bookings b
            JOIN Users u ON b.UserID = u.UserID
            JOIN Tickets tk ON b.BookingID = tk.BookingID
            JOIN Showtimes st ON tk.ShowtimeID = st.ShowtimeID
            JOIN Movies m ON st.MovieID = m.MovieID
            JOIN Seats s ON tk.SeatID = s.SeatID
            GROUP BY b.BookingID
            ORDER BY b.BookingTime DESC";

    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $bookings = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $conn->close();
    return $bookings;
}

/**
 * Lấy số liệu thống kê nhanh cho trang bookings
 * @return array Thống kê
 */
function getBookingStats() {
    $conn = getDbConnection();
    $stats = [
        'total' => 0,
        'paid' => 0,
        'pending' => 0,
        'cancelled' => 0
    ];

    // Dùng COUNT với CASE WHEN để đếm trong 1 lần query
    $sql = "SELECT 
                COUNT(*) AS total,
                COUNT(CASE WHEN PaymentStatus = 'Paid' THEN 1 END) AS paid,
                COUNT(CASE WHEN PaymentStatus = 'Pending' THEN 1 END) AS pending,
                COUNT(CASE WHEN PaymentStatus = 'Canceled' THEN 1 END) AS cancelled
            FROM Bookings";
            
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows === 1) {
        $stats = $result->fetch_assoc();
    }
    
    $conn->close();
    return $stats;
}

/**
 * Cập nhật trạng thái thanh toán
 * @param int $booking_id ID đơn hàng
 * @param string $new_status Trạng thái mới ('Paid', 'Canceled')
 * @return bool True nếu thành công
 */
function updateBookingStatus($booking_id, $new_status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE Bookings SET PaymentStatus = ? WHERE BookingID = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return false;
    }
    
    $stmt->bind_param("si", $new_status, $booking_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}
?>