<?php
// BTL_MO/View/admin/dashboard.php

include_once '../../functions/admin_gate.php';
require_once '../../functions/dashboard_functions.php';

// Lấy dữ liệu
$stats = getDashboardStats();
$recent_bookings = getRecentBookings(6); 
$top_movies = getTopMovies(5);           
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* Layout Grid cho phần dưới */
        .dashboard-grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Cột trái gấp đôi cột phải */
            gap: 24px;
        }
        
        /* Style cho Top Movies */
        .top-movie-item {
            display: flex; gap: 15px; align-items: center;
            padding: 15px 0; border-bottom: 1px solid var(--border-color);
        }
        .top-movie-item:last-child { border-bottom: none; }
        
        .tm-poster {
            width: 50px; height: 75px; object-fit: cover; border-radius: 4px;
        }
        .tm-info h4 { font-size: 14px; margin-bottom: 5px; color: #fff; }
        .tm-stats { font-size: 12px; color: #aaa; }
        .tm-highlight { color: var(--primary-color); font-weight: bold; }

        /* Responsive cho mobile */
        @media (max-width: 1024px) {
            .dashboard-grid-2 { grid-template-columns: 1fr; }
        }
        
        /* Badge trạng thái nhỏ gọn */
        .status-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .dot-Paid { background-color: #46d369; }     /* Xanh lá */
        .dot-Pending { background-color: #ffa500; }  /* Cam */
        .dot-Cancelled { background-color: #e50914; }/* Đỏ */
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="header-actions">
                    <div class="user-menu">
                        <img src="../../assets/images/default-avatar.png" alt="Admin">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(229, 9, 20, 0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Doanh thu hôm nay</span>
                            <span class="stat-value" style="color: #e50914;">
                                <?php echo number_format($stats['revenue'], 0, ',', '.'); ?> ₫
                            </span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(70, 211, 105, 0.1);">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Vé đã bán</span>
                            <span class="stat-value" style="color: #46d369;"><?php echo number_format($stats['tickets']); ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 165, 0, 0.1);">
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Người dùng mới</span>
                            <span class="stat-value" style="color: #ffa500;"><?php echo number_format($stats['users']); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1);">
                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Phim đang chiếu</span>
                            <span class="stat-value" style="color: #3b82f6;"><?php echo number_format($stats['movies']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid-2" style="margin-top: 30px;">
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Đơn hàng mới nhất</h3>
                            <a href="bookings.php" class="card-action">Xem tất cả →</a>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recent_bookings)): ?>
                                        <tr><td colspan="5" class="text-center">Chưa có đơn hàng nào.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_bookings as $bk): ?>
                                        <tr>
                                            <td>#<?php echo $bk['BookingID']; ?></td>
                                            <td>
                                                <div style="font-weight: 500;"><?php echo htmlspecialchars($bk['FullName']); ?></div>
                                            </td>
                                            <td style="color: var(--primary-color); font-weight: bold;">
                                                <?php echo number_format($bk['TotalAmount'], 0, ',', '.'); ?> ₫
                                            </td>
                                            <td>
                                                <span class="status-dot dot-<?php echo $bk['PaymentStatus']; ?>"></span>
                                                
                                                <?php 
                                                    if ($bk['PaymentStatus'] == 'Paid') echo 'Đã thanh toán';
                                                    elseif ($bk['PaymentStatus'] == 'Pending') echo 'Chờ thanh toán';
                                                    elseif ($bk['PaymentStatus'] == 'Cancelled') echo 'Đã hủy';
                                                    else echo $bk['PaymentStatus'];
                                                ?>
                                            </td>
                                            <td style="color: #888; font-size: 13px;">
                                                <?php echo date('H:i d/m', strtotime($bk['BookingTime'])); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Top phim bán chạy</h3>
                        </div>
                        <div class="top-movies-list">
                            <?php if (empty($top_movies)): ?>
                                <p style="color: #777; text-align: center;">Chưa có dữ liệu phim.</p>
                            <?php else: ?>
                                <?php foreach ($top_movies as $mv): 
                                    $poster = !empty($mv['PosterURL']) ? "../../".$mv['PosterURL'] : "../../assets/images/no-poster.jpg";
                                ?>
                                <div class="top-movie-item">
                                    <img src="<?php echo htmlspecialchars($poster); ?>" class="tm-poster">
                                    <div class="tm-info">
                                        <h4><?php echo htmlspecialchars($mv['Title']); ?></h4>
                                        <div class="tm-stats">
                                            <span class="tm-highlight"><?php echo $mv['TicketCount']; ?></span> vé đã bán
                                            <br>
                                            Doanh thu: <span style="color: #fff;"><?php echo number_format($mv['Revenue'], 0, ',', '.'); ?> ₫</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>