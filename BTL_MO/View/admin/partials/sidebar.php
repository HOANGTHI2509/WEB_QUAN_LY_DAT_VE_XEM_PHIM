<?php
// BTL_MO/View/admin/partials/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="logo">
           <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8a4 4 0 0 1 0 8v4a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-4a4 4 0 0 1 0-8"></path>
                <path d="M10 8V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v4"></path>
                <path d="M6 8v-2a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"></path>
                <path d="M18 8v-2a2 2 0 0 0-2-2h0a2 2 0 0 0-2 2v2"></path>
                <line x1="12" y1="14" x2="12" y2="22"></line>
                <line x1="8" y1="15" x2="9" y2="22"></line>
                <line x1="16" y1="15" x2="15" y2="22"></line>
            </svg>
            <span>Cinema</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>

        <div style="color: #666; font-size: 11px; font-weight: bold; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px;">Quản lý Phim</div>
        
        <a href="movies.php" class="nav-item <?php echo ($current_page == 'movies.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline>
            </svg>
            <span>Danh sách Phim</span>
        </a>

        <a href="genres.php" class="nav-item <?php echo ($current_page == 'genres.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line>
            </svg>
            <span>Thể loại Phim</span>
        </a>

        <div style="color: #666; font-size: 11px; font-weight: bold; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px;">Hệ thống Rạp</div>

        <a href="theaters.php" class="nav-item <?php echo ($current_page == 'theaters.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
            </svg>
            <span>Rạp chiếu</span>
        </a>

        <a href="screens.php" class="nav-item <?php echo ($current_page == 'screens.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
            <span>Phòng chiếu</span>
        </a>

        <a href="seat_types.php" class="nav-item <?php echo ($current_page == 'seat_types.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 21h10"></path><rect x="4" y="3" width="16" height="14" rx="2" ry="2"></rect>
            </svg>
            <span>Loại ghế & Giá</span>
        </a>

        <div style="color: #666; font-size: 11px; font-weight: bold; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px;">Kinh doanh</div>

        <a href="showtimes.php" class="nav-item <?php echo ($current_page == 'showtimes.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Lịch chiếu</span>
        </a>

        <a href="bookings.php" class="nav-item <?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <span>Đơn đặt vé</span>
        </a>

        <a href="food_combos.php" class="nav-item <?php echo ($current_page == 'food_combos.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
            <span>Đồ ăn & Combo</span>
        </a>
        
        <a href="promotions.php" class="nav-item <?php echo ($current_page == 'promotions.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <span>Khuyến mãi</span>
        </a>

        <div style="color: #666; font-size: 11px; font-weight: bold; padding: 15px 20px 5px; text-transform: uppercase; letter-spacing: 1px;">Hệ thống</div>

        <a href="users.php" class="nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Người dùng</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../user/index.php" class="nav-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <span>Về trang người dùng</span>
        </a>
        <a href="../../Handle/logout_process.php" class="nav-item" onclick="return confirm('Đăng xuất khỏi hệ thống?');">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>