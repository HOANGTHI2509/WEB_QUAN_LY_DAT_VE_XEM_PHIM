<?php
// BTL_MO/View/user/movie-detail.php

// 1. INCLUDE & LOAD DATA
require_once '../../functions/movies_functions.php';
require_once '../../functions/showtimes_functions.php';

$movie_id = $_GET['id'] ?? null;

if (!$movie_id) {
    header("Location: index.php");
    exit;
}

$movie = getMovieById((int)$movie_id);
$showtimes_data = getShowtimesByMovie((int)$movie_id); // Hàm vừa thêm

if (!$movie) {
    echo "Phim không tồn tại.";
    exit;
}

// Hàm hỗ trợ hiển thị ảnh (Copy từ index.php để đồng bộ)
function getPosterLink($url) {
    if (strpos($url, 'http') === 0) return htmlspecialchars($url);
    elseif (!empty($url)) return "../../" . htmlspecialchars($url);
    return "https://via.placeholder.com/400x600?text=No+Poster";
}

// Hàm hỗ trợ lấy Youtube ID để embed
function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? null;
}

$page_title = $movie['Title'] . " - CinemaHub";
$page_css = "home.css"; // Dùng chung CSS home để có giao diện nền đen
include 'partials/header.php';
?>

<style>
    .movie-detail-header {
        position: relative;
        padding: 80px 0;
        background-size: cover;
        background-position: center;
        color: #fff;
    }
    /* Lớp phủ làm tối nền */
    .movie-detail-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.8), #000);
    }
    .detail-content {
        position: relative;
        z-index: 2;
        display: flex;
        gap: 40px;
        align-items: flex-start;
    }
    .detail-poster {
        width: 300px;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.5);
        flex-shrink: 0;
    }
    .detail-info h1 { font-size: 42px; margin-bottom: 10px; }
    .meta-tags { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .tag { background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 4px; font-size: 14px; }
    .tag-primary { background: var(--primary-color); font-weight: bold; }
    
    .trailer-box { margin-top: 30px; }
    .trailer-frame { width: 100%; height: 400px; border-radius: 12px; border: none; background: #000; }

    /* LỊCH CHIẾU */
    .booking-section { padding: 60px 0; background: #111; }
    .date-group { margin-bottom: 30px; }
    .date-label { font-size: 18px; color: var(--primary-color); margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 5px; display: inline-block; }
    .theater-group { margin-bottom: 20px; }
    .theater-name { font-weight: bold; margin-bottom: 10px; color: #ddd; }
    .time-list { display: flex; gap: 10px; flex-wrap: wrap; }
    .time-btn {
        padding: 10px 20px;
        background: #222;
        color: #fff;
        border: 1px solid #444;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }
    .time-btn:hover { background: var(--primary-color); border-color: var(--primary-color); }
    
    @media (max-width: 768px) {
        .detail-content { flex-direction: column; align-items: center; text-align: center; }
        .meta-tags { justify-content: center; }
        .trailer-frame { height: 250px; }
    }
</style>

<div class="movie-detail-header" style="background-image: url('<?php echo getPosterLink($movie['PosterURL']); ?>');">
    <div class="container">
        <div class="detail-content">
            <img src="<?php echo getPosterLink($movie['PosterURL']); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?>" class="detail-poster">
            
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($movie['Title']); ?></h1>
                
                <div class="meta-tags">
                    <span class="tag tag-primary"><?php echo $movie['Status']; ?></span>
                    <span class="tag"><?php echo $movie['Duration']; ?> phút</span>
                    <span class="tag">Khởi chiếu: <?php echo date('d/m/Y', strtotime($movie['ReleaseDate'])); ?></span>
                </div>

                <p><strong>Đạo diễn:</strong> <?php echo htmlspecialchars($movie['Director']); ?></p>
                <div style="margin-top: 20px;">
                    <h3>Nội dung phim</h3>
                    <p style="line-height: 1.6; color: #ccc;"><?php echo nl2br(htmlspecialchars($movie['Description'])); ?></p>
                </div>

                <?php if ($movie['TrailerURL']): ?>
                <div class="trailer-box">
                    <h3>Trailer</h3>
                    <?php $yt_id = getYoutubeId($movie['TrailerURL']); ?>
                    <?php if ($yt_id): ?>
                        <iframe class="trailer-frame" src="https://www.youtube.com/embed/<?php echo $yt_id; ?>" allowfullscreen></iframe>
                    <?php else: ?>
                        <p><a href="<?php echo $movie['TrailerURL']; ?>" target="_blank" class="link">Xem trên Youtube</a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<section class="booking-section" id="booking">
    <div class="container">
        <div class="section-header">
            <h2>Lịch chiếu</h2>
        </div>

        <?php if (empty($showtimes_data)): ?>
            <div style="text-align: center; padding: 40px; border: 1px dashed #444; border-radius: 8px;">
                <p style="color: #aaa;">Hiện chưa có lịch chiếu cho phim này.</p>
            </div>
        <?php else: ?>
            <?php foreach ($showtimes_data as $date => $theaters): ?>
                <div class="date-group">
                    <h3 class="date-label">Ngày: <?php echo date('d/m/Y', strtotime($date)); ?></h3>
                    
                    <?php foreach ($theaters as $theaterName => $times): ?>
                        <div class="theater-group">
                            <div class="theater-name">📍 <?php echo htmlspecialchars($theaterName); ?></div>
                            <div class="time-list">
                                <?php foreach ($times as $st): ?>
                                    <a href="seat-selection.php?showtime=<?php echo $st['ShowtimeID']; ?>" class="time-btn">
                                        <?php echo date('H:i', strtotime($st['StartTime'])); ?>
                                        <br>
                                        <small style="font-size: 11px; font-weight: normal; color: #aaa;">
                                            <?php echo number_format($st['Price'], 0, ',', '.'); ?>đ
                                        </small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>