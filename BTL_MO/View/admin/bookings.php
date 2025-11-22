<?php
// BTL_MO/View/admin/bookings.php

// 1. KIỂM TRA QUYỀN ADMIN & GỌI HÀM
include_once '../../functions/admin_gate.php';
require_once '../../functions/bookings_functions.php';

// 2. LẤY DỮ LIỆU GỐC
$bookings_list = getAllBookings();

// 3. XỬ LÝ TÌM KIẾM (PHP FILTER)
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $filtered_list = [];
    
    // Duyệt qua danh sách gốc để lọc
    foreach ($bookings_list as $item) {
        // Tìm trong: Mã đơn, Tên khách, Email
        // stripos là hàm tìm chuỗi không phân biệt hoa thường
        if (stripos($item['BookingID'], $search) !== false || 
            stripos($item['CustomerName'], $search) !== false || 
            stripos($item['Email'], $search) !== false) {
            $filtered_list[] = $item;
        }
    }
    // Gán danh sách đã lọc đè lên danh sách hiển thị
    $bookings_list = $filtered_list;
}

// 4. TÍNH TOÁN THỐNG KÊ (Dựa trên danh sách đang hiển thị)
$stats = [
    'total' => count($bookings_list),
    'paid' => 0,
    'pending' => 0,
    'cancelled' => 0
];

foreach ($bookings_list as $b) {
    if ($b['PaymentStatus'] == 'Paid') $stats['paid']++;
    elseif ($b['PaymentStatus'] == 'Pending') $stats['pending']++;
    elseif ($b['PaymentStatus'] == 'Cancelled') $stats['cancelled']++;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .status-Paid { background: rgba(70, 211, 105, 0.2); color: #46d369; }
        .status-Pending { background: rgba(255, 165, 0, 0.2); color: #ffa500; }
        .status-Cancelled { background: rgba(229, 9, 20, 0.2); color: #e50914; }
        
        .action-btn { padding: 5px 10px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px; color: #fff; text-decoration: none; display: inline-block; }
        .btn-approve { background: #28a745; }
        .btn-cancel { background: #dc3545; }
        
        /* Style lại ô tìm kiếm một chút */
        .search-form { display: flex; align-items: center; width: 100%; }
        .search-form input { width: 100%; padding: 8px 15px; border-radius: 20px; border: 1px solid #444; background: #222; color: #fff; }
        .search-form input:focus { border-color: #e50914; outline: none; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý đơn hàng</h1>
                <div class="header-actions">
                    <div class="user-menu">
                        <img src="../../assets/images/default-avatar.png" alt="Admin">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 255, 255, 0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Hiển thị</span>
                            <span class="stat-value"><?php echo $stats['total']; ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(70, 211, 105, 0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Đã thanh toán</span>
                            <span class="stat-value"><?php echo $stats['paid']; ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 165, 0, 0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Chờ thanh toán</span>
                            <span class="stat-value"><?php echo $stats['pending']; ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Đã hủy</span>
                            <span class="stat-value"><?php echo $stats['cancelled']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <form action="" method="GET" class="search-form" style="width: 300px;">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Nhập mã đơn, tên, email..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            </form>
                        
                        <?php if(!empty($search)): ?>
                            <a href="bookings.php" class="btn-action" style="margin-left: 10px;">Xóa lọc</a>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Vé</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings_list)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding: 30px; color: #777;">
                                            Không tìm thấy đơn hàng nào<?php echo $search ? ' khớp với từ khóa "' . htmlspecialchars($search) . '"' : ''; ?>.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings_list as $item): ?>
                                    <tr>
                                        <td><strong>#<?php echo $item['BookingID']; ?></strong></td>
                                        <td>
                                            <div style="font-weight:bold;"><?php echo htmlspecialchars($item['CustomerName']); ?></div>
                                            <small style="color:#888;"><?php echo htmlspecialchars($item['Email']); ?></small>
                                        </td>
                                        <td style="color: var(--primary-color); font-weight: bold;">
                                            <?php echo number_format($item['TotalAmount'], 0, ',', '.'); ?> ₫
                                        </td>
                                        <td><?php echo $item['TicketCount']; ?> vé</td>
                                        <td>
                                            <span class="status-badge status-<?php echo $item['PaymentStatus']; ?>">
                                                <?php 
                                                if ($item['PaymentStatus'] == 'Paid') echo 'Đã thanh toán';
                                                elseif ($item['PaymentStatus'] == 'Pending') echo 'Chờ thanh toán';
                                                else echo 'Đã hủy';
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('H:i d/m/Y', strtotime($item['BookingTime'])); ?></td>
                                        <td>
                                            <?php if ($item['PaymentStatus'] == 'Pending'): ?>
                                                <form action="../../Handle/bookings_process.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="booking_id" value="<?php echo $item['BookingID']; ?>">
                                                    <input type="hidden" name="new_status" value="Paid">
                                                    <button type="submit" class="action-btn btn-approve" title="Xác nhận thanh toán">✔</button>
                                                </form>
                                                
                                                <form action="../../Handle/bookings_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Hủy đơn này?');">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="booking_id" value="<?php echo $item['BookingID']; ?>">
                                                    <input type="hidden" name="new_status" value="Cancelled">
                                                    <button type="submit" class="action-btn btn-cancel" title="Hủy đơn">✖</button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: #555; font-size: 12px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>