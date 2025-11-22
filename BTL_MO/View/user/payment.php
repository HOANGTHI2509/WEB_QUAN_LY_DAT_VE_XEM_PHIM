<?php
// BTL_MO/View/user/payment.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

require_once '../../functions/bookings_functions.php';

// 1. Lấy ID đơn hàng
$booking_id = $_GET['booking_id'] ?? 0;
$booking = getBookingDetail($booking_id);

// 2. Kiểm tra hợp lệ
if (!$booking || $booking['UserID'] != $_SESSION['user_id']) {
    die("Lỗi: Đơn hàng không tồn tại.");
}

// Tính toán hiển thị
$seat_names = [];
$seat_total = 0;
if (!empty($booking['seats'])) {
    foreach ($booking['seats'] as $s) {
        $seat_names[] = $s['RowName'] . $s['SeatNumber'];
        $seat_total += $s['Price'];
    }
}

$food_total = 0;
if (!empty($booking['foods'])) {
    foreach ($booking['foods'] as $f) {
        $food_total += $f['PriceAtBooking'] * $f['Quantity'];
    }
}

$final_total = $booking['TotalAmount']; 
$page_title = "Thanh toán - CinemaHub";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/seat-selection.css">
    <link rel="stylesheet" href="../../assets/css/food-selection.css">
    
    <style>
        /* CSS RIÊNG CHO GIAO DIỆN THANH TOÁN DARK MODE */
        .payment-section h2 {
            font-size: 24px; margin-bottom: 20px; color: #fff;
            border-bottom: 1px solid var(--border-color); padding-bottom: 15px;
        }

        /* Card chứa các phương thức thanh toán */
        .method-list { display: flex; flex-direction: column; gap: 15px; }

        .method-item { 
            display: flex; align-items: center; gap: 20px; padding: 20px; 
            background: var(--bg-secondary); /* Nền xám đậm */
            border: 1px solid var(--border-color); 
            border-radius: 8px; cursor: pointer; transition: 0.2s;
            position: relative;
        }

        /* Hiệu ứng khi hover hoặc active */
        .method-item:hover { border-color: #aaa; }
        .method-item.active { 
            border-color: var(--primary-color); 
            background: rgba(229, 9, 20, 0.1); /* Nền đỏ nhạt trong suốt */
        }
        
        /* Icon phương thức */
        .method-icon { 
            width: 50px; height: 50px; object-fit: contain; 
            background: #fff; padding: 5px; border-radius: 6px; /* Nền trắng nhỏ cho icon đỡ bị chìm */
        }

        /* Radio button tùy chỉnh */
        .radio-custom {
            width: 20px; height: 20px; border: 2px solid #aaa; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-left: auto; /* Đẩy sang phải cùng */
        }
        .radio-custom::after {
            content: ''; width: 10px; height: 10px; background: var(--primary-color);
            border-radius: 50%; display: none;
        }
        .method-item.active .radio-custom { border-color: var(--primary-color); }
        .method-item.active .radio-custom::after { display: block; }
        
        /* Input radio thật bị ẩn đi */
        .method-item input[type="radio"] { display: none; }

        /* Văn bản */
        .method-info h4 { font-size: 16px; color: #fff; margin-bottom: 5px; }
        .method-info p { font-size: 13px; color: var(--text-secondary); }

        /* Ghi chú màu vàng */
        .note-box {
            background: rgba(255, 193, 7, 0.1); border: 1px dashed #ffc107;
            color: #ffc107; padding: 15px; border-radius: 8px; margin-top: 25px;
            font-size: 13px; line-height: 1.5;
        }

        /* Phần Sidebar tổng tiền */
        .summary-block { border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 15px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: var(--text-secondary); }
        .info-row strong { color: #fff; }
        
        .final-total { font-size: 24px; color: var(--primary-color); font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo"><span>Cinema  </span></div>
                
                <div class="booking-steps">
                    <div class="step completed"><span class="step-number">1</span><span class="step-label">Suất chiếu</span></div>
                    <div class="step completed"><span class="step-number">2</span><span class="step-label">Ghế</span></div>
                    <div class="step completed"><span class="step-number">3</span><span class="step-label">Đồ ăn</span></div>
                    <div class="step active"><span class="step-number">4</span><span class="step-label">Thanh toán</span></div>
                </div>
                
                <a href="../../Handle/bookings_process.php?action=cancel&id=<?php echo $booking_id; ?>" 
                   class="btn-back" onclick="return confirm('Hủy đơn hàng này?');">Hủy đơn</a>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="container">
            <div class="booking-layout">
                
                <div class="payment-section">
                    <h2>Chọn phương thức thanh toán</h2>
                    
                    <form id="paymentForm" action="../../Handle/bookings_process.php" method="POST">
                        <input type="hidden" name="action" value="confirm_payment">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

                        <div class="method-list">
                            <label class="method-item active">
                                <input type="radio" name="payment_method" value="ATM" checked>
                                <img src="https://cdn-icons-png.flaticon.com/512/2534/2534204.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Thẻ ATM nội địa / Internet Banking</h4>
                                    <p>Hỗ trợ tất cả các ngân hàng tại Việt Nam</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>

                            <label class="method-item">
                                <input type="radio" name="payment_method" value="MOMO">
                                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Ví điện tử MoMo</h4>
                                    <p>Quét mã QR để thanh toán nhanh chóng</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>

                            <label class="method-item">
                                <input type="radio" name="payment_method" value="VISA">
                                <img src="https://cdn-icons-png.flaticon.com/512/349/349221.png" class="method-icon">
                                <div class="method-info">
                                    <h4>Thẻ Quốc tế (Visa / Master / JCB)</h4>
                                    <p>Thanh toán an toàn, bảo mật</p>
                                </div>
                                <div class="radio-custom"></div>
                            </label>
                        </div>
                        
                        <div class="note-box">
                            ⚠️ <strong>Lưu ý:</strong> Sau khi bấm "Thanh toán", đơn hàng sẽ được chuyển sang trạng thái <strong>Chờ xác nhận</strong>. Vui lòng đợi nhân viên/hệ thống xác nhận trong giây lát.
                        </div>
                    </form>
                </div>

                <div class="booking-sidebar">
                    <div class="booking-summary">
                        <h3 style="border-bottom: 1px solid #444; padding-bottom: 15px; margin-bottom: 15px;">Thông tin đặt vé</h3>
                        
                        <div class="summary-block">
                            <h4 style="color: #fff; margin-bottom: 10px; font-size: 18px;"><?php echo htmlspecialchars($booking['MovieTitle']); ?></h4>
                            <div class="info-row"><span>Rạp:</span> <strong><?php echo htmlspecialchars($booking['TheaterName']); ?></strong></div>
                            <div class="info-row"><span>Suất:</span> <strong><?php echo date('H:i d/m', strtotime($booking['StartTime'])); ?></strong></div>
                            <div class="info-row"><span>Phòng:</span> <strong><?php echo htmlspecialchars($booking['ScreenName']); ?></strong></div>
                            <div class="info-row"><span>Ghế:</span> <strong style="color: var(--primary-color);"><?php echo implode(', ', $seat_names); ?></strong></div>
                        </div>

                        <div style="margin-top: 20px;">
                            <div class="info-row">
                                <span>Tổng vé:</span> 
                                <span><?php echo number_format($seat_total, 0, ',', '.'); ?> ₫</span>
                            </div>
                            <div class="info-row">
                                <span>Tổng đồ ăn:</span> 
                                <span><?php echo number_format($food_total, 0, ',', '.'); ?> ₫</span>
                            </div>
                            
                            <div style="border-top: 1px dashed #555; margin: 15px 0;"></div>
                            
                            <div class="info-row" style="align-items: center;">
                                <span style="font-size: 16px; font-weight: bold; color: #fff;">Tổng thanh toán:</span>
                                <span class="final-total"><?php echo number_format($final_total, 0, ',', '.'); ?> ₫</span>
                            </div>
                        </div>

                        <button type="button" onclick="submitPayment()" class="btn-continue">
                            THANH TOÁN NGAY
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function submitPayment() {
            if(confirm('Xác nhận thanh toán đơn hàng này?')) {
                document.getElementById('paymentForm').submit();
            }
        }
        
        // Hiệu ứng JS đổi class active khi click
        const methods = document.querySelectorAll('.method-item');
        methods.forEach(m => {
            m.addEventListener('click', () => {
                // Bỏ active ở tất cả
                methods.forEach(x => x.classList.remove('active'));
                // Thêm active vào cái được click
                m.classList.add('active');
                // Check radio button bên trong
                m.querySelector('input').checked = true;
            });
        });
    </script>
</body>
</html>