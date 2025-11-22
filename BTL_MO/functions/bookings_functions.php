<?php
// BTL_MO/functions/bookings_functions.php
require_once 'db_connect.php';

/**
 * 1. Lấy chi tiết 1 đơn hàng (Dùng cho trang Thanh toán / Thành công)
 */
function getBookingDetail($booking_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT b.*, u.FullName, u.Email, 
                   COALESCE(m.Title, 'Phim không xác định') as MovieTitle, 
                   st.StartTime, st.ShowtimeID, 
                   COALESCE(t.Name, 'Rạp mặc định') as TheaterName, 
                   COALESCE(sc.Name, 'Phòng chiếu') as ScreenName,
                   st.Price as BasePrice
            FROM Bookings b
            JOIN Users u ON b.UserID = u.UserID
            LEFT JOIN Tickets tk ON b.BookingID = tk.BookingID
            LEFT JOIN Showtimes st ON tk.ShowtimeID = st.ShowtimeID
            LEFT JOIN Movies m ON st.MovieID = m.MovieID
            LEFT JOIN Screens sc ON st.ScreenID = sc.ScreenID
            LEFT JOIN Theaters t ON sc.TheaterID = t.TheaterID
            WHERE b.BookingID = ?
            LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if (!$booking) return null;

    // Lấy danh sách ghế
    $stmt_seats = $conn->prepare("SELECT s.RowName, s.SeatNumber, t.Price 
                                  FROM Tickets t 
                                  JOIN Seats s ON t.SeatID = s.SeatID 
                                  WHERE t.BookingID = ?");
    $stmt_seats->bind_param("i", $booking_id);
    $stmt_seats->execute();
    $booking['seats'] = $stmt_seats->get_result()->fetch_all(MYSQLI_ASSOC);

    // Lấy danh sách đồ ăn
    $stmt_food = $conn->prepare("SELECT f.Name, bf.Quantity, bf.PriceAtBooking 
                                 FROM Booking_Food bf 
                                 JOIN FoodCombos f ON bf.FoodID = f.FoodID 
                                 WHERE bf.BookingID = ?");
    $stmt_food->bind_param("i", $booking_id);
    $stmt_food->execute();
    $booking['foods'] = $stmt_food->get_result()->fetch_all(MYSQLI_ASSOC);

    return $booking;
}

/**
 * 2. Tạo đơn hàng ban đầu (Trạng thái Pending - Giữ ghế)
 */
function createInitialBooking($data) {
    $conn = getDbConnection();
    $conn->begin_transaction();

    try {
        $user_id = $data['user_id'];
        $showtime_id = $data['showtime_id'];
        $seat_ids = $data['seat_ids'];
        
        if (empty($seat_ids)) throw new Exception("Chưa chọn ghế.");

        // Lấy giá vé gốc
        $stmt_price = $conn->prepare("SELECT Price FROM Showtimes WHERE ShowtimeID = ?");
        $stmt_price->bind_param("i", $showtime_id);
        $stmt_price->execute();
        $res_price = $stmt_price->get_result()->fetch_assoc();
        
        if (!$res_price) throw new Exception("Suất chiếu không hợp lệ");
        $base_price = $res_price['Price'];
        
        $total_amount = 0;

        // Tạo Booking
        $stmt_book = $conn->prepare("INSERT INTO Bookings (UserID, TotalAmount, BookingTime, PaymentStatus) VALUES (?, 0, NOW(), 'Pending')");
        $stmt_book->bind_param("i", $user_id);
        if (!$stmt_book->execute()) throw new Exception("Lỗi tạo Booking");
        
        $booking_id = $conn->insert_id;

        // Tạo Tickets
        $stmt_ticket = $conn->prepare("INSERT INTO Tickets (BookingID, ShowtimeID, SeatID, Price) VALUES (?, ?, ?, ?)");
        
        foreach ($seat_ids as $seat_id) {
            // Check ghế trống
            $check = $conn->query("SELECT t.TicketID FROM Tickets t JOIN Bookings b ON t.BookingID = b.BookingID 
                                   WHERE t.ShowtimeID = $showtime_id AND t.SeatID = $seat_id 
                                   AND (b.PaymentStatus = 'Paid' OR (b.PaymentStatus = 'Pending' AND b.BookingTime >= (NOW() - INTERVAL 10 MINUTE)))");
            if ($check->num_rows > 0) throw new Exception("Ghế bạn chọn đã bị đặt.");

            // Tính giá vé (Có phụ thu)
            $q_seat = $conn->query("SELECT PriceSurcharge FROM SeatTypes st JOIN Seats s ON st.SeatTypeID = s.SeatTypeID WHERE s.SeatID = $seat_id");
            $surcharge = ($q_seat && $q_seat->num_rows > 0) ? $q_seat->fetch_assoc()['PriceSurcharge'] : 0;
            
            $ticket_price = $base_price + $surcharge;
            $total_amount += $ticket_price;

            $stmt_ticket->bind_param("iiid", $booking_id, $showtime_id, $seat_id, $ticket_price);
            if (!$stmt_ticket->execute()) throw new Exception("Lỗi tạo vé");
        }

        // Cập nhật tổng tiền
        $conn->query("UPDATE Bookings SET TotalAmount = $total_amount WHERE BookingID = $booking_id");

        $conn->commit();
        return ['success' => true, 'booking_id' => $booking_id];

    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * 3. Cập nhật đồ ăn vào đơn hàng
 */
function updateBookingFood($booking_id, $foods) {
    $conn = getDbConnection();
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM Booking_Food WHERE BookingID = $booking_id");
        $food_total = 0;
        if (!empty($foods)) {
            $stmt_bf = $conn->prepare("INSERT INTO Booking_Food (BookingID, FoodID, Quantity, PriceAtBooking) VALUES (?, ?, ?, ?)");
            foreach ($foods as $food_id => $qty) {
                if ($qty > 0) {
                    $q_food = $conn->query("SELECT Price FROM FoodCombos WHERE FoodID = $food_id");
                    $price = $q_food->fetch_assoc()['Price'];
                    $food_total += ($price * $qty);
                    $stmt_bf->bind_param("iiid", $booking_id, $food_id, $qty, $price);
                    $stmt_bf->execute();
                }
            }
        }
        // Cập nhật lại tổng tiền (lấy từ vé + đồ ăn mới)
        $res_ticket = $conn->query("SELECT SUM(Price) as TTotal FROM Tickets WHERE BookingID = $booking_id")->fetch_assoc();
        $ticket_total = $res_ticket['TTotal'] ?? 0;
        $conn->query("UPDATE Bookings SET TotalAmount = " . ($ticket_total + $food_total) . " WHERE BookingID = $booking_id");
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback(); return false;
    }
}

/**
 * 4. Lấy danh sách TẤT CẢ đơn hàng (Dùng cho Admin)
 * (Đây là hàm bạn bị thiếu gây ra lỗi)
 */
function getAllBookings() {
    $conn = getDbConnection();
    // Cập nhật: Lấy thêm Email
    $sql = "SELECT b.*, u.FullName as CustomerName, u.Email,
            (SELECT COUNT(*) FROM Tickets WHERE BookingID = b.BookingID) as TicketCount
            FROM Bookings b 
            JOIN Users u ON b.UserID = u.UserID 
            ORDER BY b.BookingTime DESC";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

/**
 * 5. Lấy lịch sử đặt vé của 1 User (Đã fix lỗi Group By)
 */
function getBookingsByUserId($user_id) {
    $conn = getDbConnection();
    
    // Dùng DISTINCT để tránh lỗi ONLY_FULL_GROUP_BY
    $sql = "SELECT DISTINCT b.BookingID, b.TotalAmount, b.BookingTime, b.PaymentStatus,
                   m.Title as MovieTitle, m.PosterURL,
                   t.Name as TheaterName, sc.Name as ScreenName,
                   st.StartTime
            FROM Bookings b
            JOIN Tickets tk ON b.BookingID = tk.BookingID
            JOIN Showtimes st ON tk.ShowtimeID = st.ShowtimeID
            JOIN Movies m ON st.MovieID = m.MovieID
            JOIN Screens sc ON st.ScreenID = sc.ScreenID
            JOIN Theaters t ON sc.TheaterID = t.TheaterID
            WHERE b.UserID = ?
            ORDER BY b.BookingTime DESC";
            
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Lỗi truy vấn SQL (bookings_functions.php): " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $b_id = $row['BookingID'];
        
        // Lấy danh sách ghế cho từng Booking
        $seat_sql = "SELECT s.RowName, s.SeatNumber 
                     FROM Tickets tk 
                     JOIN Seats s ON tk.SeatID = s.SeatID 
                     WHERE tk.BookingID = $b_id";
        
        $seats_res = $conn->query($seat_sql);
        if ($seats_res) {
            $seats = [];
            while ($s = $seats_res->fetch_assoc()) {
                $seats[] = $s['RowName'] . $s['SeatNumber'];
            }
            $row['Seats'] = implode(', ', $seats);
        } else {
            $row['Seats'] = "Lỗi ghế";
        }
        
        $bookings[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $bookings;
}
?>