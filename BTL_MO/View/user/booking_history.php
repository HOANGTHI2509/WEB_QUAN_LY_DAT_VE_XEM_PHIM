<?php
// BTL_MO/View/user/booking_history.php
session_start();

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

// 2. CHẶN ADMIN TRUY CẬP (Logic mới thêm)
// Admin không có lịch sử đặt vé cá nhân ở giao diện này, nên đẩy về Dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
    header("location: ../admin/dashboard.php");
    exit;
}

require_once '../../functions/bookings_functions.php';

// 3. LẤY DỮ LIỆU VÉ CỦA USER
$my_bookings = getBookingsByUserId($_SESSION['user_id']);

// Hàm hỗ trợ hiển thị ảnh poster
function getPosterLink($url) {
    if (empty($url)) return "https://via.placeholder.com/150x200?text=No+Poster";
    if (strpos($url, 'http') === 0) return htmlspecialchars($url);
    return "../../" . htmlspecialchars($url);
}

$page_title = "Lịch sử đặt vé";
$page_css = "home.css"; // Dùng chung CSS với trang chủ
include 'partials/header.php';
?>

<style>
    /* CSS RIÊNG CHO TRANG LỊCH SỬ */
    .history-section { padding: 60px 0 80px; min-height: 80vh; }
    .page-header { margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px; }
    .page-header h1 { font-size: 32px; margin-bottom: 5px; color: #e50914; }
    .page-header p { color: #aaa; font-size: 14px; }
    
    .booking-card {
        background: var(--bg-secondary);
        border-radius: 8px; padding: 20px;
        display: grid; grid-template-columns: 100px 1fr auto; gap: 20px;
        margin-bottom: 20px; border: 1px solid var(--border-color);
        transition: transform 0.2s;
    }
    .booking-card:hover { transform: translateY(-3px); border-color: #555; }
    
    .booking-poster { width: 100px; height: 150px; border-radius: 6px; overflow: hidden; flex-shrink: 0; }
    .booking-poster img { width: 100%; height: 100%; object-fit: cover; }
    
    .booking-details { display: flex; flex-direction: column; justify-content: center; }
    .booking-details h3 { font-size: 18px; margin-bottom: 8px; color: #fff; }
    
    .info-line { display: flex; align-items: center; gap: 8px; color: #ccc; font-size: 14px; margin-bottom: 6px; }
    .info-line svg { width: 16px; height: 16px; color: #e50914; }
    
    .booking-status { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; display: inline-block; margin-top: 5px; }
    .status-Paid { background: rgba(70, 211, 105, 0.15); color: #46d369; border: 1px solid #46d369; }
    .status-Pending { background: rgba(255, 165, 0, 0.15); color: #ffa500; border: 1px solid #ffa500; }
    .status-Cancelled { background: rgba(229, 9, 20, 0.15); color: #e50914; border: 1px solid #e50914; }
    
    .booking-actions { text-align: right; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; }
    .price-tag { font-size: 18px; font-weight: bold; color: #e50914; margin-bottom: 10px; }
    
    .btn-view {
        padding: 8px 20px; background: #333; color: #fff;
        border: 1px solid #555; border-radius: 4px; cursor: pointer; 
        text-decoration: none; font-size: 13px; font-weight: 600;
        transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-view:hover { background: #fff; color: #000; }

    @media (max-width: 768px) {
        .booking-card { grid-template-columns: 1fr; }
        .booking-poster { width: 100%; height: 200px; }
        .booking-actions { flex-direction: row; align-items: center; margin-top: 15px; border-top: 1px dashed #444; padding-top: 15px; }
    }
</style>

<section class="history-section">
    <div class="container">
        <div class="page-header">
            <h1>Lịch sử đặt vé</h1>
            <p>Quản lý danh sách vé đã đặt và trạng thái thanh toán của bạn.</p>
        </div>

        <?php if (empty($my_bookings)): ?>
            <div style="text-align: center; padding: 60px 20px; background: #1a1a1a; border-radius: 12px;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="1" style="margin-bottom: 15px;">
                    <rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline>
                </svg>
                <p style="color: #777; margin-bottom: 20px;">Bạn chưa có đơn đặt vé nào.</p>
                <a href="index.php" class="btn-primary">Đặt vé ngay</a>
            </div>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($my_bookings as $bk): ?>
                    <div class="booking-card">
                        <div class="booking-poster">
                            <img src="<?php echo getPosterLink($bk['PosterURL']); ?>" alt="Poster">
                        </div>
                        
                        <div class="booking-details">
                            <h3><?php echo htmlspecialchars($bk['MovieTitle']); ?></h3>
                            
                            <div class="info-line">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span><?php echo date('H:i - d/m/Y', strtotime($bk['StartTime'])); ?></span>
                            </div>
                            
                            <div class="info-line">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?php echo htmlspecialchars($bk['TheaterName']); ?> - <?php echo htmlspecialchars($bk['ScreenName']); ?></span>
                            </div>
                            
                            <div class="info-line">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 21h10"></path><rect x="4" y="3" width="16" height="14" rx="2" ry="2"></rect></svg>
                                <span>Ghế: <strong style="color: #fff;"><?php echo $bk['Seats']; ?></strong></span>
                            </div>

                            <div>
                                <span class="booking-status status-<?php echo $bk['PaymentStatus']; ?>">
                                    <?php 
                                    if ($bk['PaymentStatus'] == 'Paid') echo 'Đã thanh toán';
                                    elseif ($bk['PaymentStatus'] == 'Pending') echo 'Chờ xác nhận';
                                    else echo 'Đã hủy';
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="booking-actions">
                            <div class="price-tag">
                                <?php echo number_format($bk['TotalAmount'], 0, ',', '.'); ?> ₫
                            </div>
                            
                            <?php if ($bk['PaymentStatus'] == 'Paid'): ?>
                                <a href="booking-success.php?id=<?php echo $bk['BookingID']; ?>" class="btn-view">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Xem vé
                                </a>
                            <?php elseif ($bk['PaymentStatus'] == 'Pending'): ?>
                                <a href="booking-success.php?id=<?php echo $bk['BookingID']; ?>" class="btn-view" style="color: #ffa500; border-color: #ffa500;">
                                    Chi tiết đơn
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>