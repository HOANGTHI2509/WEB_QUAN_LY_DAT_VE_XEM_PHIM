<?php
// BTL_MO/View/user/showtimes.php

// 1. INCLUDE CÁC FILE CẦN THIẾT
require_once '../../functions/movies_functions.php';
require_once '../../functions/showtimes_functions.php';

// 2. LẤY ID PHIM
$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    header("Location: index.php");
    exit;
}

// 3. LẤY THÔNG TIN
$movie = getMovieById((int)$movie_id);
$showtimes_data = getShowtimesByMovie((int)$movie_id);

if (!$movie) {
    echo "Phim không tồn tại.";
    exit;
}

// Hàm hỗ trợ hiển thị ảnh (Giống index.php)
function getPosterLink($url) {
    if (strpos($url, 'http') === 0) return htmlspecialchars($url);
    elseif (!empty($url)) return "../../" . htmlspecialchars($url);
    return "https://via.placeholder.com/400x600?text=No+Poster";
}

$page_title = "Lịch chiếu - " . $movie['Title'];
$page_css = "home.css"; // Dùng chung CSS home
include 'partials/header.php';
?>

<style>
    /* CSS RIÊNG CHO TRANG LỊCH CHIẾU */
    .movie-header-bg {
        position: relative;
        padding: 60px 0;
        background: #111;
        border-bottom: 1px solid #333;
    }
    .movie-header-content {
        display: flex;
        gap: 30px;
        align-items: center;
    }
    .header-poster {
        width: 180px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }
    .date-section {
        margin-bottom: 40px;
    }
    .date-header {
        font-size: 20px;
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        display: inline-block;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    .theater-block {
        background: var(--bg-secondary);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .theater-name {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .time-list {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .time-btn {
        padding: 10px 25px;
        background: #333;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: 1px solid #555;
        text-align: center;
        transition: all 0.3s;
    }
    .time-btn:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }
    .time-val { font-size: 16px; font-weight: bold; display: block; }
    .price-val { font-size: 12px; color: #ccc; }
    
    @media (max-width: 768px) {
        .movie-header-content { flex-direction: column; text-align: center; }
    }
</style>

<div class="movie-header-bg">
    <div class="container">
        <div class="movie-header-content">
            <img src="<?php echo getPosterLink($movie['PosterURL']); ?>" class="header-poster">
            <div>
                <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($movie['Title']); ?></h1>
                <p style="color: #aaa;">Thời lượng: <?php echo $movie['Duration']; ?> phút</p>
                <p style="color: #aaa;">Đạo diễn: <?php echo htmlspecialchars($movie['Director']); ?></p>
            </div>
        </div>
    </div>
</div>

<main class="section">
    <div class="container">
        <?php if (empty($showtimes_data)): ?>
            <div style="text-align: center; padding: 50px; background: var(--bg-secondary); border-radius: 8px;">
                <h3>Hiện chưa có lịch chiếu nào cho phim này.</h3>
                <p style="color: #aaa; margin-top: 10px;">Vui lòng quay lại sau.</p>
                <a href="index.php" class="btn-primary" style="margin-top: 20px; display: inline-block;">Quay về trang chủ</a>
            </div>
        <?php else: ?>
            
            <?php foreach ($showtimes_data as $date => $theaters): ?>
                <div class="date-section">
                    <h3 class="date-header">
                        📅 Ngày: <?php echo date('d/m/Y', strtotime($date)); ?>
                    </h3>

                    <?php foreach ($theaters as $theaterName => $times): ?>
                        <div class="theater-block">
                            <div class="theater-name">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <?php echo htmlspecialchars($theaterName); ?>
                            </div>
                            <div class="time-list">
                                <?php foreach ($times as $st): ?>
                                    <a href="seat-selection.php?showtime=<?php echo $st['ShowtimeID']; ?>" class="time-btn">
                                        <span class="time-val"><?php echo date('H:i', strtotime($st['StartTime'])); ?></span>
                                        <span class="price-val"><?php echo number_format($st['Price'], 0, ',', '.'); ?>đ</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</main>

<?php include 'partials/footer.php'; ?>