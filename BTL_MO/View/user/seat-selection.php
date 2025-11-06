<?php
// BTL_MO/View/user/seat-selection.php

// 1. BẮT BUỘC ĐĂNG NHẬP
include_once '../../functions/auth.php'; // Sẽ tự động chuyển hướng nếu chưa login
// 2. GỌI CÁC HÀM CẦN THIẾT
require_once '../../functions/showtimes_functions.php';

// 3. LẤY DỮ LIỆU SUẤT CHIẾU
$showtime_id = $_GET['showtime'] ?? null;
if (empty($showtime_id)) {
    header("location: index.php?error=no_showtime");
    exit;
}

$showtime_detail = getShowtimeDetail((int)$showtime_id);
if (!$showtime_detail) {
    header("location: index.php?error=invalid_showtime");
    exit;
}

// 4. LẤY DỮ LIỆU GHẾ
$seats_data = getSeatsForShowtime($showtime_detail['ShowtimeID'], $showtime_detail['ScreenID']);

// 5. TỔ CHỨC LẠI GHẾ THEO HÀNG (để PHP render)
$seat_rows = [];
foreach ($seats_data as $seat) {
    $seat_rows[$seat['RowName']][] = $seat;
}

// 6. THIẾT LẬP GIÁ VÉ (để JS sử dụng)
$seatPrices = [
    'normal' => (float)$showtime_detail['Price'],
    'vip' => (float)$showtime_detail['Price'] + 20000 // Giả sử VIP đắt hơn 20k
    // (Bạn nên thêm cột PriceSurcharge vào SeatTypes và cộng vào đây)
];

// 7. CÀI ĐẶT BIẾN CHO HEADER/FOOTER
$page_css = "seat-selection.css";
$page_js = "seat-selection.js";

// (Trang này dùng layout riêng, không dùng header/footer chung)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - CinemaHub</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/seat-selection.css">
    
    <script>
      const showtimeId = <?php echo json_encode($showtime_detail['ShowtimeID']); ?>;
      const seatPrices = <?php echo json_encode($seatPrices); ?>;
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content" style="justify-content: space-between;">
                <div class="logo">
                    <svg width="32" height="32"...></svg>
                    <span>CinemaHub</span>
                </div>
                <div class="booking-steps">
                    <div class="step active">
                        <span class="step-number">1</span>
                        <span class="step-label">Chọn ghế</span>
                    </div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-label">Thanh toán</span>
                    </div>
                </div>
                <a href="index.php" class="btn-back">Quay lại</a>
            </div>
        </div>
    </nav>

    <main class="booking-container">
        <div class="container">
            <form action="../../Handle/booking_process.php" method="POST">
                
                <input type="hidden" name="showtime_id" value="<?php echo $showtime_detail['ShowtimeID']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                
                <div class="booking-layout">
                    <div class="screen-section">
                        <div class="showtime-info" id="showtimeInfo">
                            <div class="info-item">
                                <div>
                                    <span class="label">Phim</span>
                                    <span class="value"><?php echo htmlspecialchars($showtime_detail['MovieTitle']); ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div>
                                    <span class="label">Suất chiếu</span>
                                    <span class="value"><?php echo date('d/m/Y H:i', strtotime($showtime_detail['StartTime'])); ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div>
                                    <span class="label">Rạp</span>
                                    <span class="value"><?php echo htmlspecialchars($showtime_detail['TheaterName']); ?> (<?php echo htmlspecialchars($showtime_detail['ScreenName']); ?>)</span>
                                </div>
                            </div>
                        </div>

                        <div class="screen-wrapper">
                            <div class="screen">
                                <svg viewBox="0 0 200 30" preserveAspectRatio="none">
                                    <path d="M0,30 Q100,0 200,30 L200,30 L0,30 Z" fill="currentColor"/>
                                </svg>
                                <span>MÀN HÌNH</span>
                            </div>

                            <div class="seats-container" id="seatsContainer">
                                <?php foreach ($seat_rows as $rowName => $seats): ?>
                                    <div class="seat-row">
                                        <span class="row-label"><?php echo $rowName; ?></span>
                                        <?php foreach ($seats as $seat): ?>
                                            <?php
                                            // Xác định class cho ghế
                                            $seat_class = '';
                                            $is_occupied = $seat['IsBooked'];
                                            $is_vip = ($seat['SeatType'] == 'VIP');
                                            
                                            if ($is_occupied) {
                                                $seat_class = 'occupied';
                                            } elseif ($is_vip) {
                                                $seat_class = 'vip';
                                            } else {
                                                $seat_class = 'available';
                                            }
                                            ?>
                                            <div class="seat <?php echo $seat_class; ?>"
                                                data-seat-id="<?php echo $seat['SeatID']; ?>"
                                                data-seat-name="<?php echo $seat['RowName'] . $seat['SeatNumber']; ?>"
                                                data-seat-type="<?php echo $seat['SeatType']; ?>"
                                                <?php echo $is_occupied ? 'disabled' : ''; ?>
                                                onclick="toggleSeat(this)"> <?php echo $seat['SeatNumber']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="seat-legend">...</div>
                        </div>
                    </div>

                    <div class="booking-sidebar">
                        <div class="booking-summary">
                            <h3>Thông tin đặt vé</h3>
                            <div class="summary-section">
                                <h4>Ghế đã chọn</h4>
                                <div id="selectedSeats" class="selected-seats-list">
                                    <p class="empty-message">Chưa chọn ghế</p>
                                </div>
                                <div id="seatFormInputContainer"></div>
                            </div>

                            <div class="summary-section">
                                <div class="price-row">
                                    <span>Tổng tiền ghế</span>
                                    <span id="seatsTotal">0 ₫</span>
                                </div>
                                <div class="price-row total">
                                    <span>Tổng cộng</span>
                                    <span id="totalPrice">0 ₫</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-continue" id="btnContinue" disabled>
                                Tiếp tục
                            </button>
                        </div>

                        <div class="countdown-timer">
                            <span>Thời gian giữ ghế: <strong id="countdown">10:00</strong></span>
                        </div>
                    </div>
                </div>
            </form> </div>
    </main>

    <script src="../../assets/js/all_effects.js"></script>
    <script src="../../assets/js/seat-selection.js"></script>
</body>
</html>