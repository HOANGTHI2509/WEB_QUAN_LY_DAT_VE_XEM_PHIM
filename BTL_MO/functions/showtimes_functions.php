<?php
// BTL_MO/functions/showtimes_functions.php
require_once 'db_connect.php';

/**
 * Lấy danh sách suất chiếu của một phim (Gom nhóm theo Ngày và Rạp)
 */
function getShowtimesByMovie($movie_id) {
    $conn = getDbConnection();
    $showtimes = [];
    
    $sql = "SELECT st.ShowtimeID, st.StartTime, st.Price, 
                   t.Name as TheaterName, s.Name as ScreenName
            FROM Showtimes st
            JOIN Screens s ON st.ScreenID = s.ScreenID
            JOIN Theaters t ON s.TheaterID = t.TheaterID
            WHERE st.MovieID = ? AND st.StartTime >= NOW()
            ORDER BY st.StartTime ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row['StartTime']));
            $showtimes[$date][$row['TheaterName']][] = $row;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $showtimes;
}

/**
 * Lấy danh sách suất chiếu (cho trang admin)
 */
function getFilteredShowtimes($filters = []) {
    $conn = getDbConnection();
    $showtimes = [];
    
    $sql = "SELECT 
                st.ShowtimeID, st.StartTime, st.Price,
                m.Title AS MovieTitle, m.PosterURL,
                t.Name AS TheaterName,
                sc.Name AS ScreenName,
                (sc.Capacity - COUNT(tk.TicketID)) AS SeatsAvailable,
                sc.Capacity
            FROM Showtimes st
            JOIN Movies m ON st.MovieID = m.MovieID
            JOIN Screens sc ON st.ScreenID = sc.ScreenID
            JOIN Theaters t ON sc.TheaterID = t.TheaterID
            LEFT JOIN Tickets tk ON st.ShowtimeID = tk.ShowtimeID
            WHERE 1=1";

    $params = [];
    $types = "";

    if (!empty($filters['date'])) {
        $sql .= " AND DATE(st.StartTime) = ?";
        $params[] = $filters['date'];
        $types .= "s";
    }
    if (!empty($filters['movie_id'])) {
        $sql .= " AND st.MovieID = ?";
        $params[] = $filters['movie_id'];
        $types .= "i";
    }
    if (!empty($filters['theater_id'])) {
        $sql .= " AND t.TheaterID = ?";
        $params[] = $filters['theater_id'];
        $types .= "i";
    }

    $sql .= " GROUP BY st.ShowtimeID ORDER BY st.StartTime ASC";

    $stmt = $conn->prepare($sql);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $showtimes = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    $conn->close();
    return $showtimes;
}

/**
 * Lấy thông tin chi tiết 1 suất chiếu
 */
function getShowtimeDetail($showtime_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT 
                st.ShowtimeID, st.StartTime, st.Price,
                m.Title AS MovieTitle, m.Duration,
                t.Name AS TheaterName,
                sc.Name AS ScreenName, sc.ScreenID
            FROM Showtimes st
            JOIN Movies m ON st.MovieID = m.MovieID
            JOIN Screens sc ON st.ScreenID = sc.ScreenID
            JOIN Theaters t ON sc.TheaterID = t.TheaterID
            WHERE st.ShowtimeID = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $detail = null;
    if ($result && $result->num_rows === 1) {
        $detail = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $detail;
}

/**
 * Lấy sơ đồ ghế với trạng thái THỰC TẾ (Sold, Held, Available)
 */
function getSeatsForShowtime($showtime_id, $screen_id) {
    $conn = getDbConnection();
    
    // SQL NÂNG CẤP:
    // - Kiểm tra bảng Tickets để xem ghế đã có vé chưa
    // - Join bảng Bookings để xem vé đó đã trả tiền hay đang giữ
    // - Logic: 
    //    + Paid -> Sold (Đỏ)
    //    + Pending & < 10 phút -> Held (Xanh)
    //    + Pending & > 10 phút -> Coi như NULL (Trống)
    
    $sql = "SELECT 
                s.SeatID, s.RowName, s.SeatNumber, s.SeatTypeID, 
                st.Name AS SeatType, st.PriceSurcharge,
                (CASE 
                    WHEN b.PaymentStatus = 'Paid' THEN 'sold'
                    WHEN b.PaymentStatus = 'Pending' AND b.BookingTime >= (NOW() - INTERVAL 10 MINUTE) THEN 'held'
                    ELSE 'available'
                END) AS Status
            FROM Seats s
            JOIN SeatTypes st ON s.SeatTypeID = st.SeatTypeID
            LEFT JOIN Tickets tk ON s.SeatID = tk.SeatID AND tk.ShowtimeID = ?
            LEFT JOIN Bookings b ON tk.BookingID = b.BookingID
            WHERE s.ScreenID = ?
            ORDER BY LENGTH(s.RowName), s.RowName, s.SeatNumber";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $showtime_id, $screen_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Thêm suất chiếu mới
 */
function addShowtime($data) {
    $conn = getDbConnection();
    
    $movie_id = $data['movie_id'];
    $screen_id = $data['screen_id'];
    $price = $data['price'];
    $start_time = $data['show_date'] . ' ' . $data['show_time']; 

    $sql = "INSERT INTO Showtimes (MovieID, ScreenID, StartTime, Price) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisd", $movie_id, $screen_id, $start_time, $price);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        if (strpos($error, 'Duplicate entry') !== false) {
            return "Lỗi: Đã có suất chiếu tại phòng này vào thời điểm này.";
        }
        return "Thêm suất chiếu thất bại: " . $error;
    }
}

/**
 * Cập nhật suất chiếu
 */
function updateShowtime($data) {
    $conn = getDbConnection();
    
    $showtime_id = $data['showtime_id'];
    $movie_id = $data['movie_id'];
    $screen_id = $data['screen_id'];
    $price = $data['price'];
    $start_time = $data['show_date'] . ' ' . $data['show_time'];

    $sql = "UPDATE Showtimes SET MovieID = ?, ScreenID = ?, StartTime = ?, Price = ?
            WHERE ShowtimeID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdi", $movie_id, $screen_id, $start_time, $price, $showtime_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Cập nhật thất bại: " . $error;
    }
}

/**
 * Xóa suất chiếu
 */
function deleteShowtime($showtime_id) {
    $conn = getDbConnection();
    
    $sql_check = "SELECT COUNT(*) AS TicketCount FROM Tickets WHERE ShowtimeID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $showtime_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $ticket_count = $result_check->fetch_assoc()['TicketCount'];
    $stmt_check->close();

    if ($ticket_count > 0) {
        $conn->close();
        return ['success' => false, 'message' => 'Không thể xóa suất chiếu này vì đã có vé được bán.'];
    }

    $sql_delete = "DELETE FROM Showtimes WHERE ShowtimeID = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $showtime_id);

    if ($stmt_delete->execute()) {
        $success = $stmt_delete->affected_rows > 0;
        $message = $success ? 'Xóa suất chiếu thành công!' : 'Không tìm thấy suất chiếu để xóa.';
    } else {
        $message = 'Xóa thất bại: ' . $stmt_delete->error;
        $success = false;
    }
    
    $stmt_delete->close();
    $conn->close();
    return ['success' => $success, 'message' => $message];
}

// Hàm hỗ trợ cho get_screens_api.php (Lấy phòng theo rạp)
function getScreensByTheater($theater_id) {
    $conn = getDbConnection();
    $screens = [];
    $sql = "SELECT ScreenID, Name, Capacity FROM Screens WHERE TheaterID = ? ORDER BY Name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $screens = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
    $conn->close();
    return $screens;
}
?>