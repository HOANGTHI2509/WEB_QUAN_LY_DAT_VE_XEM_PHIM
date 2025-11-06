<?php
// BTL_MO/View/user/partials/header.php
// Luôn bắt đầu session ở file đầu tiên được include
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Lấy tên file hiện tại để active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="../../assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2.5 16.5l19-9m-19 0l19 9M7 5v14m10-14v14"></path>
                    </svg>
                    <span>CinemaHub</span>
                </div>

                <div class="nav-links">
                    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Trang chủ</a>
                    <a href="movies.php" class="<?php echo ($current_page == 'movies.php') ? 'active' : ''; ?>">Phim</a>
                    <a href="theaters.php" class="<?php echo ($current_page == 'theaters.php') ? 'active' : ''; ?>">Rạp chiếu</a>
                    <a href="promotions.php" class="<?php echo ($current_page == 'promotions.php') ? 'active' : ''; ?>">Khuyến mãi</a>
                </div>

                <div class="nav-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Tìm phim..." id="searchInput">
                        <svg width="20" height="20"...></svg>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) : ?>
                        <span style="color: #fff; margin-right: 15px; font-weight: 500;">
                            Chào, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
                        </span>
                        
                        <?php if ($_SESSION['role'] == 'Admin'): ?>
                            <a href="../admin/dashboard.php" class="btn-login" style="color: var(--warning-color);">Trang Admin</a>
                        <?php endif; ?>
                        
                        <button onclick="logout()" class="btn-primary">Đăng xuất</button>
                        
                    <?php else : ?>
                        <a href="login.php" class="btn-login">Đăng nhập</a>
                        <a href="register.php" class="btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>