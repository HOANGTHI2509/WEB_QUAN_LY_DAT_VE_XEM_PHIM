<?php
// BTL_MO/View/user/booking-success.php
session_start();
require_once '../../functions/bookings_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

$booking_id = $_GET['id'] ?? 0;
$booking = getBookingDetail($booking_id);

// Kiểm tra bảo mật
if (!$booking || $booking['UserID'] != $_SESSION['user_id']) {
    die("Không tìm thấy đơn hàng.");
}

$status = $booking['PaymentStatus'];
$page_title = ($status == 'Paid') ? "Đặt vé thành công" : "Chờ xác nhận";
$page_css = "home.css";
include 'partials/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
    .result-container { padding: 60px 0; min-height: 80vh; display: flex; justify-content: center; align-items: center; }
    
    /* GIAO DIỆN CHỜ XỬ LÝ (PENDING) */
    .pending-card {
        background: var(--bg-secondary); border: 1px solid #b8860b; 
        border-radius: 16px; padding: 40px; text-align: center; max-width: 600px; width: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .pending-icon { color: #ffc107; margin-bottom: 20px; }
    .pending-card h1 { color: #ffc107; font-size: 28px; margin-bottom: 10px; }
    .pending-card p { color: #ccc; margin-bottom: 20px; font-size: 16px; line-height: 1.6; }
    .order-ref { background: rgba(255, 255, 255, 0.1); padding: 10px; border-radius: 8px; display: inline-block; margin-bottom: 30px; color: #fff; font-family: monospace; font-size: 18px; }
    
    /* GIAO DIỆN VÉ THÀNH CÔNG (PAID) */
    .ticket-wrapper { text-align: center; }
    .ticket-card {
        background: #fff; color: #333; width: 400px; margin: 0 auto 30px;
        border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        position: relative;
    }
    .ticket-header { background: #e50914; color: #fff; padding: 20px; }
    .ticket-body { padding: 30px; text-align: left; }
    .tk-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; border-bottom: 1px dashed #ddd; padding-bottom: 8px; }
    .tk-val { font-weight: bold; text-align: right; color: #000; }
    .qr-area { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 2px dashed #333; }
    
    .btn-group { display: flex; gap: 15px; justify-content: center; }
    .btn-action { padding: 12px 25px; border-radius: 30px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; transition: 0.3s; }
    .btn-home { background: #333; color: #fff; border: 1px solid #555; }
    .btn-save { background: #46d369; color: #fff; }
    .btn-reload { background: #e50914; color: #fff; }
    .btn-action:hover { transform: translateY(-2px); opacity: 0.9; }
</style>

<main class="section">
    <div class="container result-container">

        <?php if ($status == 'Pending'): ?>
            <div class="pending-card">
                <div class="pending-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h1>Đơn hàng đang chờ xác nhận</h1>
                <p>
                    Cảm ơn bạn đã đặt vé. Hệ thống đang xử lý giao dịch của bạn.<br>
                    Vui lòng đợi nhân viên xác nhận thanh toán để nhận vé điện tử.
                </p>
                
                <div class="order-ref">Mã đơn: #<?php echo $booking['BookingID']; ?></div>
                
                <p style="font-size: 14px; color: #888;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 5px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Lưu ý: Vé chưa có hiệu lực cho đến khi trạng thái là "Thanh toán thành công".
                </p>

                <div class="btn-group">
                    <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                    <button onclick="location.reload()" class="btn-action btn-reload">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        Kiểm tra lại
                    </button>
                </div>
            </div>

        <?php elseif ($status == 'Paid'): ?>
            <div class="ticket-wrapper">
                <div style="margin-bottom: 30px;">
                    <div style="color: #46d369; margin-bottom: 10px;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h1 style="font-size: 32px;">Đặt vé thành công!</h1>
                    <p style="color: #aaa;">Cảm ơn bạn đã sử dụng dịch vụ của CinemaHub</p>
                </div>

                <div class="ticket-card" id="ticketCapture">
                    <div class="ticket-header">
                        <h2 style="margin:0; font-size: 24px; text-transform: uppercase;">Vé Xem Phim</h2>
                        <div style="font-size: 13px; margin-top: 5px;">Mã đơn: #<?php echo $booking['BookingID']; ?></div>
                    </div>
                    
                    <div class="ticket-body">
                        <h3 style="color: #e50914; font-size: 22px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <?php echo htmlspecialchars($booking['MovieTitle']); ?>
                        </h3>
                        
                        <div class="tk-row"><span>Rạp chiếu</span> <span class="tk-val"><?php echo htmlspecialchars($booking['TheaterName']); ?></span></div>
                        <div class="tk-row"><span>Phòng chiếu</span> <span class="tk-val"><?php echo htmlspecialchars($booking['ScreenName']); ?></span></div>
                        <div class="tk-row"><span>Suất chiếu</span> <span class="tk-val"><?php echo date('H:i - d/m/Y', strtotime($booking['StartTime'])); ?></span></div>
                        <div class="tk-row">
                            <span>Ghế ngồi</span> 
                            <span class="tk-val" style="font-size: 16px;">
                                <?php 
                                $seats = array_map(function($s) { return $s['RowName'].$s['SeatNumber']; }, $booking['seats']);
                                echo implode(', ', $seats); 
                                ?>
                            </span>
                        </div>

                        <?php if (!empty($booking['foods'])): ?>
                        <div class="tk-row">
                            <span>Combo</span>
                            <span class="tk-val" style="font-weight: normal;">
                                <?php foreach($booking['foods'] as $f) echo $f['Name'] . " (x" . $f['Quantity'] . ")<br>"; ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <div class="tk-row" style="border: none; margin-top: 15px;">
                            <span style="font-weight: bold; color: #555;">Tổng thanh toán</span>
                            <span class="tk-val" style="font-size: 20px; color: #e50914;">
                                <?php echo number_format($booking['TotalAmount'], 0, ',', '.'); ?> ₫
                            </span>
                        </div>

                        <div class="qr-area">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=BOOKING-<?php echo $booking['BookingID']; ?>-VERIFIED" alt="QR Code" style="width: 140px;">
                            <p style="font-size: 11px; color: #888; margin-top: 8px;">Đưa mã này cho nhân viên để vào rạp</p>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="index.php" class="btn-action btn-home">Về trang chủ</a>
                    <button id="btnSaveImage" class="btn-action btn-save">📸 Lưu ảnh vé</button>
                </div>
            </div>

        <?php else: ?>
            <div style="text-align: center; color: #e50914;">
                <h1>Đơn hàng đã bị hủy</h1>
                <p>Vui lòng đặt lại vé mới.</p>
                <a href="index.php" class="btn-action btn-home" style="margin-top: 20px; display: inline-block;">Quay về trang chủ</a>
            </div>
        <?php endif; ?>

    </div>
</main>

<script>
    const btnSave = document.getElementById('btnSaveImage');
    if (btnSave) {
        btnSave.addEventListener('click', function() {
            const ticketElement = document.getElementById('ticketCapture');
            html2canvas(ticketElement, { scale: 2, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'CinemaHub-Ticket-<?php echo $booking_id; ?>.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        });
    }
</script>

<?php include 'partials/footer.php'; ?>