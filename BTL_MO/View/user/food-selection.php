<?php
// BTL_MO/View/user/food-selection.php
session_start();

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

require_once '../../functions/food_functions.php';
require_once '../../functions/bookings_functions.php'; 

// 2. LẤY ID ĐƠN HÀNG TỪ URL
$booking_id = $_GET['booking_id'] ?? 0;

if (!$booking_id) {
    echo "<script>alert('Lỗi: Không tìm thấy đơn hàng.'); window.location.href = 'index.php';</script>";
    exit;
}

// 3. LẤY CHI TIẾT ĐƠN HÀNG
$booking = getBookingDetail($booking_id);

if (!$booking || $booking['UserID'] != $_SESSION['user_id'] || $booking['PaymentStatus'] != 'Pending') {
    echo "<script>alert('Đơn hàng không hợp lệ hoặc đã hết hạn!'); window.location.href = 'index.php';</script>";
    exit;
}

// TÍNH TOÁN DỮ LIỆU
$total_seat_price = 0;
$seat_names = [];
if (!empty($booking['seats'])) {
    foreach ($booking['seats'] as $s) {
        $total_seat_price += $s['Price'];
        $seat_names[] = $s['RowName'] . $s['SeatNumber'];
    }
}

$food_list = getAllFoodCombos(); 
$page_title = "Chọn đồ ăn - CinemaHub";

// --- HÀM XỬ LÝ ẢNH (QUAN TRỌNG) ---
function getFoodThumb($url) {
    // 1. Nếu không có ảnh -> Trả về ảnh mặc định
    if (empty($url)) {
        return 'https://via.placeholder.com/150x150?text=No+Image';
    }
    // 2. Nếu là link online (http/https) -> Giữ nguyên
    if (strpos($url, 'http') === 0) {
        return htmlspecialchars($url);
    }
    // 3. Nếu là file upload (assets/...) -> Thêm ../../ để lùi về thư mục gốc
    return "../../" . htmlspecialchars($url);
}
// ------------------------------------
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/seat-selection.css"> 
    <link rel="stylesheet" href="../../assets/css/food-selection.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo"><span>Cinema</span></div>
                
                <div class="booking-steps">
                    <div class="step completed"><span class="step-number">1</span><span class="step-label">Chọn suất</span></div>
                    <div class="step completed"><span class="step-number">2</span><span class="step-label">Chọn ghế</span></div>
                    <div class="step active"><span class="step-number">3</span><span class="step-label">Đồ ăn</span></div>
                    <div class="step"><span class="step-number">4</span><span class="step-label">Thanh toán</span></div>
                </div>
                
                <a href="../../Handle/bookings_process.php?action=cancel&id=<?php echo $booking_id; ?>" 
                   class="btn-back" onclick="return confirm('Bạn có chắc muốn hủy đặt vé không?');">
                    Hủy đơn
                </a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="container">
            <div class="booking-layout">
                
                <div class="food-section">
                    <div class="section-header">
                        <h2>Combo Bắp Nước & Ưu đãi</h2>
                        <p>Thêm đồ ăn để trải nghiệm điện ảnh trọn vẹn hơn</p>
                    </div>

                    <div class="food-grid">
                        <?php if (empty($food_list)): ?>
                            <p class="text-center" style="grid-column: 1/-1; padding: 50px; color: #aaa;">Hiện chưa có món ăn nào.</p>
                        <?php else: ?>
                            <?php foreach ($food_list as $item): ?>
                                <div class="food-card">
                                    <div class="food-img">
                                        <img src="<?php echo getFoodThumb($item['ImageURL']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['Name']); ?>"
                                             onerror="this.src='https://via.placeholder.com/150x150?text=Error';">
                                    </div>
                                    <div class="food-info">
                                        <h3><?php echo htmlspecialchars($item['Name']); ?></h3>
                                        <p class="desc"><?php echo htmlspecialchars($item['Description']); ?></p>
                                        <div class="price"><?php echo number_format($item['Price'], 0, ',', '.'); ?> ₫</div>
                                        
                                        <div class="qty-control" 
                                             data-id="<?php echo $item['FoodID']; ?>" 
                                             data-price="<?php echo $item['Price']; ?>" 
                                             data-name="<?php echo htmlspecialchars($item['Name']); ?>">
                                            
                                            <button type="button" class="btn-qty minus" onclick="updateFood(this, -1)">-</button>
                                            <span class="qty-val">0</span>
                                            <button type="button" class="btn-qty plus" onclick="updateFood(this, 1)">+</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="booking-sidebar">
                    <div class="booking-summary">
                        <h3>Thông tin đặt vé</h3>
                        
                        <div class="summary-block">
                            <h4 class="movie-title"><?php echo htmlspecialchars($booking['MovieTitle']); ?></h4>
                            <div class="info-row"><span>Rạp:</span> <strong><?php echo htmlspecialchars($booking['TheaterName']); ?></strong></div>
                            <div class="info-row"><span>Suất:</span> <strong><?php echo date('H:i d/m', strtotime($booking['StartTime'])); ?></strong></div>
                            <div class="info-row"><span>Phòng:</span> <strong><?php echo htmlspecialchars($booking['ScreenName']); ?></strong></div>
                            <div class="info-row"><span>Ghế:</span> <strong style="color: var(--primary-color);"><?php echo implode(', ', $seat_names); ?></strong></div>
                        </div>

                        <div class="summary-block" id="selectedFoodContainer" style="display:none; border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: 15px;">
                            <h4 style="font-size: 14px; color: var(--text-secondary); margin-bottom: 10px;">Đồ ăn</h4>
                            <div id="selectedFoodList"></div>
                        </div>

                        <div class="summary-section" style="border-top: 1px solid var(--border-color); margin-top: 20px; padding-top: 20px;">
                            <div class="price-row"><span>Tiền vé:</span> <span><?php echo number_format($total_seat_price, 0, ',', '.'); ?> ₫</span></div>
                            <div class="price-row"><span>Tiền đồ ăn:</span> <span id="foodTotalDisplay">0 ₫</span></div>
                            <div class="price-row total"><span>Tổng cộng</span> <span id="grandTotalDisplay"><?php echo number_format($total_seat_price, 0, ',', '.'); ?> ₫</span></div>
                        </div>

                        <form action="../../Handle/bookings_process.php" method="POST" id="finalForm">
                            <input type="hidden" name="action" value="update_food">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                            
                            <div id="foodInputs"></div>

                            <button type="submit" class="btn-continue">
                                Xác nhận & Thanh toán
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const seatTotal = <?php echo $total_seat_price; ?>;
    </script>
    <script src="../../assets/js/food-selection.js"></script>
</body>
</html>