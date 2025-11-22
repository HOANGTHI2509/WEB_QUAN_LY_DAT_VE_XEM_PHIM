<?php
// BTL_MO/Handle/bookings_process.php
session_start();
require_once '../functions/bookings_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../View/user/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // 1. GIỮ GHẾ (Từ seat-selection.php)
        if ($action == 'reserve_seats') {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'showtime_id' => $_POST['showtime_id'],
                'seat_ids' => $_POST['seat_ids'] ?? []
            ];
            $result = createInitialBooking($data);

            if ($result['success']) {
                header("location: ../View/user/food-selection.php?booking_id=" . $result['booking_id']);
            } else {
                $error_msg = urlencode($result['message']);
                header("location: ../View/user/seat-selection.php?showtime=" . $data['showtime_id'] . "&error=" . $error_msg);
            }
            exit;
        }

        // 2. CẬP NHẬT ĐỒ ĂN (Từ food-selection.php) -> CHUYỂN SANG PAYMENT
        if ($action == 'update_food') {
            $booking_id = $_POST['booking_id'];
            $foods = $_POST['foods'] ?? [];
            
            $update_res = updateBookingFood($booking_id, $foods);
            
            if ($update_res) {
                // QUAN TRỌNG: Chuyển hướng sang trang Thanh toán
                header("location: ../View/user/payment.php?booking_id=" . $booking_id);
            } else {
                header("location: ../View/user/food-selection.php?booking_id=$booking_id&error=update_failed");
            }
            exit;
        }

        // 3. XÁC NHẬN THANH TOÁN (Từ payment.php)
        if ($action == 'confirm_payment') {
    $booking_id = $_POST['booking_id'];
    
    // LƯU Ý: Ở đây KHÔNG được update trạng thái thành Paid.
    // Trạng thái mặc định trong Database phải là 'Pending'.
    
    // Chỉ chuyển hướng người dùng sang trang chờ
    header("location: ../View/user/booking-success.php?id=" . $booking_id);
    exit;
}

        // 4. ADMIN DUYỆT ĐƠN / HỦY ĐƠN
        if ($action == 'update_status') {
            require_once '../functions/admin_gate.php';
            if (isset($_POST['booking_id']) && isset($_POST['new_status'])) {
                $conn = getDbConnection();
                $stmt = $conn->prepare("UPDATE Bookings SET PaymentStatus = ? WHERE BookingID = ?");
                $stmt->bind_param("si", $_POST['new_status'], $_POST['booking_id']);
                $stmt->execute();
                $conn->close();
                header("location: ../View/admin/bookings.php?success=update");
            }
            exit;
        }
    }
}

// Hủy đơn (GET)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'cancel') {
    $booking_id = $_GET['id'] ?? 0;
    if ($booking_id) {
        $conn = getDbConnection();
        $conn->query("DELETE FROM Tickets WHERE BookingID = $booking_id");
        $conn->query("DELETE FROM Booking_Food WHERE BookingID = $booking_id");
        $conn->query("DELETE FROM Bookings WHERE BookingID = $booking_id");
        $conn->close();
    }
    header("location: ../View/user/index.php");
    exit;
}

header("location: ../index.php");
exit;
?>