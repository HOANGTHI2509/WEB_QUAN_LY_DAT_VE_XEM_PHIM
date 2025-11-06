<?php
// BTL_MO/functions/theaters_functions.php
// [Dựa trên file bạn cung cấp, bổ sung hàm 'get']

require_once 'db_connect.php';

/**
 * Lấy tất cả rạp, kèm theo số lượng phòng chiếu
 * @return array Danh sách rạp
 */
function getAllTheaters() {
    $conn = getDbConnection();
    $theaters = [];

    // Câu lệnh JOIN để đếm số phòng (Screens) của mỗi rạp (Theaters)
    $sql = "SELECT t.*, COUNT(s.ScreenID) AS TotalScreens
            FROM Theaters t
            LEFT JOIN Screens s ON t.TheaterID = s.TheaterID
            GROUP BY t.TheaterID
            ORDER BY t.Name";
            
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $theaters = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $conn->close();
    return $theaters;
}

/**
 * Lấy thông tin 1 rạp
 * @param int $theater_id ID rạp
 * @return array|null Dữ liệu rạp
 */
function getTheaterById($theater_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM Theaters WHERE TheaterID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $theater = null;
    if ($result->num_rows === 1) {
        $theater = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $theater;
}

/**
 * Thêm rạp mới
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function addTheater($data) {
    $conn = getDbConnection();
    
    $name = $data['Name'] ?? '';
    $address = $data['Address'] ?? '';
    $city = $data['City'] ?? ''; 
    $phone = $data['Phone'] ?? null;
    $email = $data['Email'] ?? null;
    
    $sql = "INSERT INTO Theaters (Name, Address, City, Phone, Email) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return "Lỗi chuẩn bị SQL: " . $conn->error;
    }

    $stmt->bind_param("sssss", $name, $address, $city, $phone, $email); 

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Thêm rạp thất bại: " . $error; 
    }
}

/**
 * Cập nhật rạp
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function updateTheater($data) {
    $conn = getDbConnection();
    
    $theater_id = $data['TheaterID'];
    $name = $data['Name'] ?? '';
    $address = $data['Address'] ?? '';
    $city = $data['City'] ?? '';
    $phone = $data['Phone'] ?? null;
    $email = $data['Email'] ?? null;
    
    $sql = "UPDATE Theaters SET Name = ?, Address = ?, City = ?, Phone = ?, Email = ? 
            WHERE TheaterID = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return "Lỗi chuẩn bị SQL: " . $conn->error;
    }

    $stmt->bind_param("sssssi", $name, $address, $city, $phone, $email, $theater_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Cập nhật rạp thất bại: " . $error;
    }
}

/**
 * Xóa rạp
 * @param int $theater_id ID rạp
 * @return array [success (bool), message (string)]
 */
function deleteTheater($theater_id) {
    $conn = getDbConnection();
    
    // Bắt đầu giao dịch để đảm bảo xóa (hoặc không xóa) cả 2 bảng
    $conn->begin_transaction();
    
    try {
        // 1. Xóa các ghế (Seats) thuộc phòng (Screens) của rạp này
        // (Phải xóa Seats trước Screens)
        $sql_delete_seats = "DELETE s FROM Seats s 
                             JOIN Screens sc ON s.ScreenID = sc.ScreenID 
                             WHERE sc.TheaterID = ?";
        $stmt_seats = $conn->prepare($sql_delete_seats);
        $stmt_seats->bind_param("i", $theater_id);
        $stmt_seats->execute();
        $stmt_seats->close();

        // 2. Xóa các phòng chiếu (Screens) liên quan
        $sql_delete_screens = "DELETE FROM Screens WHERE TheaterID = ?";
        $stmt_screens = $conn->prepare($sql_delete_screens);
        $stmt_screens->bind_param("i", $theater_id);
        $stmt_screens->execute();
        $stmt_screens->close();

        // 3. Xóa rạp (Theaters)
        $sql_delete_theater = "DELETE FROM Theaters WHERE TheaterID = ?";
        $stmt_theater = $conn->prepare($sql_delete_theater);
        $stmt_theater->bind_param("i", $theater_id);

        if (!$stmt_theater->execute()) {
            throw new Exception("Lỗi xóa rạp: " . $stmt_theater->error);
        }

        if ($stmt_theater->affected_rows === 0) {
            $conn->rollback();
            $message = 'Không tìm thấy rạp để xóa.';
            $success = false;
        } else {
            $conn->commit(); // Hoàn tất giao dịch
            $message = 'Xóa rạp và các phòng/ghế liên quan thành công!';
            $success = true;
        }
        $stmt_theater->close();

    } catch (mysqli_sql_exception $e) {
        $conn->rollback(); // Hoàn tác nếu có lỗi
        if ($e->getCode() == 1451) {
             $message = 'Không thể xóa rạp vì nó có suất chiếu hoặc đơn đặt vé liên quan.';
             $success = false;
        } else {
             $message = 'Lỗi máy chủ: ' . $e->getMessage();
             $success = false;
        }
    } finally {
        if (isset($conn)) $conn->close();
    }
    
    return ['success' => $success, 'message' => $message];
}
?>