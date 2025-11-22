<?php
// BTL_MO/View/user/partials/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CinemaHub'; ?></title>
    
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="../../assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="index.php" class="logo" style="text-decoration: none;">
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
                </a>

                <div class="nav-links">
                    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Trang chủ</a>
                    <a href="movies.php" class="<?php echo ($current_page == 'movies.php') ? 'active' : ''; ?>">Phim</a>
                    <a href="theaters.php" class="<?php echo ($current_page == 'theaters.php') ? 'active' : ''; ?>">Rạp chiếu</a>
                    <a href="promotions.php" class="<?php echo ($current_page == 'promotions.php') ? 'active' : ''; ?>">Khuyến mãi</a>
                </div>

                <div class="nav-actions">
                    <form action="search_results.php" method="GET" class="search-box">
                        <input type="text" placeholder="Tìm phim..." id="searchInput" name="query" required>
                    </form>
                    
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) : ?>
                        <div class="nav-user-dropdown" id="userMenuDropdown">
                            <button class="nav-user-btn" id="userMenuButton" type="button">
                                <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            
                            <div class="dropdown-content" id="userMenuContent">
                                
                                <?php if ($_SESSION['role'] == 'Admin'): ?>
                                    <a href="../admin/dashboard.php">Trang Admin</a>
                                <?php else: ?>
                                    <a href="booking_history.php">Lịch sử vé đặt</a>
                                <?php endif; ?>
                                
                                <a href="../../Handle/logout_process.php">Đăng xuất</a>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="login.php" class="btn-login">Đăng nhập</a>
                        <a href="register.php" class="btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>