<?php
// BTL_MO/functions/dashboard_functions.php
require_once 'db_connect.php';

/**
 * 1. Lấy 4 chỉ số thống kê cơ bản (Card)
 */
function getDashboardStats() {
    $conn = getDbConnection();
    $stats = [];

    // a. Doanh thu hôm nay (Chỉ tính đơn Paid)
    $sql = "SELECT SUM(TotalAmount) as Val FROM Bookings WHERE PaymentStatus = 'Paid' AND DATE(BookingTime) = CURDATE()";
    $stats['revenue'] = $conn->query($sql)->fetch_assoc()['Val'] ?? 0;

    // b. Vé bán ra (Tổng cộng)
    $sql = "SELECT COUNT(t.TicketID) as Val FROM Tickets t JOIN Bookings b ON t.BookingID = b.BookingID WHERE b.PaymentStatus = 'Paid'";
    $stats['tickets'] = $conn->query($sql)->fetch_assoc()['Val'] ?? 0;

    // c. Người dùng mới (Tháng này)
    $sql = "SELECT COUNT(UserID) as Val FROM Users WHERE Role = 'User' AND MONTH(CreatedAt) = MONTH(CURDATE()) AND YEAR(CreatedAt) = YEAR(CURDATE())";
    $stats['users'] = $conn->query($sql)->fetch_assoc()['Val'] ?? 0;

    // d. Phim đang chiếu
    $sql = "SELECT COUNT(MovieID) as Val FROM Movies WHERE Status = 'Đang chiếu'";
    $stats['movies'] = $conn->query($sql)->fetch_assoc()['Val'] ?? 0;

    $conn->close();
    return $stats;
}

/**
 * 2. Lấy danh sách đơn hàng mới nhất
 * @param int $limit Số lượng cần lấy
 */
function getRecentBookings($limit = 5) {
    $conn = getDbConnection();
    $sql = "SELECT b.BookingID, b.TotalAmount, b.PaymentStatus, b.BookingTime, 
                   u.FullName, u.Email
            FROM Bookings b
            JOIN Users u ON b.UserID = u.UserID
            ORDER BY b.BookingTime DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    return $result;
}

/**
 * 3. Lấy Top phim bán chạy nhất (Dựa trên số vé bán ra)
 */
function getTopMovies($limit = 5) {
    $conn = getDbConnection();
    // Logic: Join từ Phim -> Suất chiếu -> Vé -> Đơn hàng (Chỉ tính đơn đã thanh toán)
    $sql = "SELECT m.Title, m.PosterURL, COUNT(tk.TicketID) as TicketCount, SUM(tk.Price) as Revenue
            FROM Movies m
            LEFT JOIN Showtimes st ON m.MovieID = st.MovieID
            LEFT JOIN Tickets tk ON st.ShowtimeID = tk.ShowtimeID
            LEFT JOIN Bookings b ON tk.BookingID = b.BookingID
            WHERE b.PaymentStatus = 'Paid'
            GROUP BY m.MovieID
            ORDER BY TicketCount DESC
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    return $result;
}
?>