<?php
// BTL_MO/View/user/index.php

// 1. GỌI CÁC HÀM FUNCTIONS
require_once '../../functions/movies_functions.php';
require_once '../../functions/promotions_functions.php';

// 2. LẤY DỮ LIỆU TỪ CSDL
$all_movies = getAllMovies();
$all_promotions = getAllPromotions(); 

// 3. LỌC DỮ LIỆU PHIM
$now_showing_movies = [];
$coming_soon_movies = [];

foreach ($all_movies as $movie) {
    if ($movie['Status'] == 'Đang chiếu') {
        $now_showing_movies[] = $movie;
    } elseif ($movie['Status'] == 'Sắp chiếu') {
        $coming_soon_movies[] = $movie;
    }
}

// --- HÀM HỖ TRỢ HIỂN THỊ ẢNH (QUAN TRỌNG) ---
function getPosterLink($url) {
    // Nếu link bắt đầu bằng 'http', giữ nguyên (link ngoài)
    if (strpos($url, 'http') === 0) {
        return htmlspecialchars($url);
    }
    // Nếu là file upload, thêm đường dẫn tương đối từ thư mục user
    elseif (!empty($url)) {
        return "../../" . htmlspecialchars($url);
    }
    // Ảnh mặc định nếu không có
    return "https://via.placeholder.com/400x600?text=No+Poster";
}
// ---------------------------------------------

// 4. INCLUDE HEADER
$page_css = "home.css";
$page_title = "CinemaHub - Đặt vé xem phim online";
include 'partials/header.php';
?>

<section class="hero">
    <div class="hero-slider">
        <div class="hero-slide active" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url('https://images.pexels.com/photos/7991579/pexels-photo-7991579.jpeg?auto=compress&cs=tinysrgb&w=1920') center/cover;">
            <div class="container">
                <div class="hero-content">
                    <h1>Trải nghiệm điện ảnh đỉnh cao</h1>
                    <p>Đặt vé online nhanh chóng - Nhận ưu đãi hấp dẫn</p>
                    <div class="hero-buttons">
                        <a href="#now-showing" class="btn-hero-primary">Đặt vé ngay</a>
                        <a href="movies.php" class="btn-hero-secondary">Xem tất cả phim</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-dots">
        <span class="dot active"></span><span class="dot"></span><span class="dot"></span>
    </div>
</section>

<section class="quick-booking"></section>

<section id="now-showing" class="section">
    <div class="container">
        <div class="section-header">
            <h2>Phim đang chiếu</h2>
            <a href="movies.php?filter=now-showing" class="view-all">
                Xem tất cả
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        </div>

        <div class="movie-grid" id="nowShowingMovies">
    <?php if (empty($now_showing_movies)): ?>
        <p style="color: var(--text-secondary);">Hiện chưa có phim nào đang chiếu.</p>
    <?php else: ?>
        <?php foreach (array_slice($now_showing_movies, 0, 8) as $movie): ?>
            <div class="movie-card">
                <div class="movie-poster">
                    <img src="<?php echo getPosterLink($movie['PosterURL']); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                    
                    <div class="movie-overlay">
                        <a href="movie-detail.php?id=<?php echo $movie['MovieID']; ?>" class="overlay-btn btn-detail">Chi tiết</a>
                        
                        <a href="showtimes.php?movie_id=<?php echo $movie['MovieID']; ?>" class="overlay-btn btn-buy-overlay">Đặt vé</a>
                    </div>
                </div>
                
                <div class="movie-info">
                    <h3><?php echo htmlspecialchars($movie['Title']); ?></h3>
                    <div class="movie-meta">
                        <span class="duration"><?php echo $movie['Duration']; ?> phút</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <h2>Phim sắp chiếu</h2>
            <a href="movies.php?filter=coming-soon" class="view-all">Xem tất cả</a>
        </div>
        <div class="movie-grid" id="comingSoonMovies">
             <?php if (empty($coming_soon_movies)): ?>
                <p style="color: var(--text-secondary);">Hiện chưa có phim nào sắp chiếu.</p>
            <?php else: ?>
                <?php foreach (array_slice($coming_soon_movies, 0, 8) as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img src="<?php echo getPosterLink($movie['PosterURL']); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                            <div class="movie-overlay"></div>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['Title']); ?></h3>
                            <div class="movie-meta">
                                <span class="duration"><?php echo $movie['Duration']; ?> phút</span>
                            </div>
                            <button class="btn-book-ticket" disabled style="background: var(--bg-tertiary); cursor: not-allowed;">Chưa mở bán</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Khuyến mãi hot</h2>
            <a href="promotions.php" class="view-all">Xem tất cả</a>
        </div>
        <div class="promo-grid">
            <?php if (empty($all_promotions)): ?>
                <p style="color: var(--text-secondary);">Hiện chưa có khuyến mãi nào.</p>
            <?php else: ?>
                <?php foreach (array_slice($all_promotions, 0, 3) as $promo): ?>
                    <div class="promo-card">
                        <img src="https://via.placeholder.com/400x200?text=Promotion" alt="<?php echo htmlspecialchars($promo['Code']); ?>">
                        <div class="promo-content">
                            <span class="promo-badge">
                                <?php 
                                if (!empty($promo['DiscountPercent']) && $promo['DiscountPercent'] > 0) {
                                    echo "Giảm " . $promo['DiscountPercent'] . "%";
                                } else {
                                    echo "Giảm " . number_format($promo['DiscountValue'], 0, ',', '.') . " ₫";
                                }
                                ?>
                            </span>
                            <h3>Mã: <?php echo htmlspecialchars($promo['Code']); ?></h3>
                            <p>HSD: <?php echo date('d/m/Y', strtotime($promo['EndDate'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>