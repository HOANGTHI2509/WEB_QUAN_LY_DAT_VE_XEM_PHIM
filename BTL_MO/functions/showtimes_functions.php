<?php
// BTL_MO/functions/showtimes_functions.php

require_once 'db_connect.php';

/* ============================================== */
/* CÁC HÀM LẤY DỮ LIỆU (GET) */
/* ============================================== */

/**
 * Lấy danh sách suất chiếu (cho trang admin)
 * @param array $filters (chứa 'date', 'movie_id', 'theater_id')
 * @return array Danh sách suất chiếu
 */
function getFilteredShowtimes($filters = []) {
    $conn = getDbConnection();
    $showtimes = [];
    
    // Câu lệnh SQL phức tạp để JOIN 5 bảng
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
            WHERE 1=1"; // Bắt đầu mệnh đề WHERE

    $params = [];
    $types = "";

    // Thêm bộ lọc ngày
    if (!empty($filters['date'])) {
        $sql .= " AND DATE(st.StartTime) = ?";
        $params[] = $filters['date'];
        $types .= "s";
    }

    // Thêm bộ lọc phim
    if (!empty($filters['movie_id'])) {
        $sql .= " AND st.MovieID = ?";
        $params[] = $filters['movie_id'];
        $types .= "i";
    }
    
    // Thêm bộ lọc rạp
    if (!empty($filters['theater_id'])) {
        $sql .= " AND t.TheaterID = ?";
        $params[] = $filters['theater_id'];
        $types .= "i";
    }

    $sql .= " GROUP BY st.ShowtimeID ORDER BY st.StartTime ASC";

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $showtimes = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    $conn->close();
    return $showtimes;
}

/**
 * Lấy thông tin chi tiết 1 suất chiếu (cho trang đặt vé)
 * @param int $showtime_id ID suất chiếu
 * @return array|null Dữ liệu suất chiếu
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
    if ($result->num_rows === 1) {
        $detail = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $detail;
}

/**
 * Lấy sơ đồ ghế ngồi cho 1 suất chiếu
 * @param int $showtime_id ID suất chiếu
 * @param int $screen_id ID phòng chiếu
 * @return array Danh sách ghế
 */
function getSeatsForShowtime($showtime_id, $screen_id) {
    $conn = getDbConnection();
    $seats = [];

    // Lấy TẤT CẢ ghế của phòng (ScreenID)
    // Sau đó JOIN với bảng Tickets để xem ghế nào ĐÃ ĐƯỢC ĐẶT (IsBooked)
    // CHỈ cho suất chiếu này (ShowtimeID)
    $sql = "SELECT 
                s.SeatID, s.RowName, s.SeatNumber,
                st.Name AS SeatType,
                st.PriceSurcharge,
                (CASE WHEN tk.TicketID IS NOT NULL THEN 1 ELSE 0 END) AS IsBooked
            FROM Seats s
            JOIN SeatTypes st ON s.SeatTypeID = st.SeatTypeID
            LEFT JOIN Tickets tk ON s.SeatID = tk.SeatID AND tk.ShowtimeID = ?
            WHERE s.ScreenID = ?
            ORDER BY s.RowName, s.SeatNumber";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $showtime_id, $screen_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $seats = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
    $conn->close();
    return $seats;
}

/**
 * Lấy danh sách phòng chiếu của 1 rạp (cho admin)
 * @param int $theater_id ID rạp
 * @return array Danh sách phòng chiếu
 */
function getScreensByTheater($theater_id) {
    $conn = getDbConnection();
    $screens = [];

    $sql = "SELECT ScreenID, Name, Capacity FROM Screens WHERE TheaterID = ? ORDER BY Name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $screens = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    $conn->close();
    return $screens;
}

/* ============================================== */
/* CÁC HÀM XỬ LÝ (CRUD) */
/* ============================================== */

/**
 * Thêm suất chiếu mới
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
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
        // Kiểm tra lỗi trùng lặp (ví dụ: cùng phòng, cùng giờ)
        if (strpos($error, 'Duplicate entry') !== false) {
            return "Lỗi: Đã có suất chiếu tại phòng này vào thời điểm này.";
        }
        return "Thêm suất chiếu thất bại: " . $error;
    }
}

/**
 * Cập nhật suất chiếu
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
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
 * @param int $showtime_id ID suất chiếu
 * @return array [success (bool), message (string)]
 */
function deleteShowtime($showtime_id) {
    $conn = getDbConnection();
    
    // 1. Kiểm tra xem đã có ai đặt vé cho suất này chưa
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

    // 2. Nếu chưa có vé, tiến hành xóa
    $sql_delete = "DELETE FROM Showtimes WHERE ShowtimeID = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $showtime_id);

    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $message = 'Xóa suất chiếu thành công!';
            $success = true;
        } else {
            $message = 'Không tìm thấy suất chiếu để xóa.';
            $success = false;
        }
    } else {
        $message = 'Xóa thất bại: ' . $stmt_delete->error;
        $success = false;
    }
    
    $stmt_delete->close();
    $conn->close();
    return ['success' => $success, 'message' => $message];
}
?>