<?php
// BTL_MO/View/user/movies.php

require_once '../../functions/movies_functions.php';

// 1. Lấy bộ lọc (nếu có)
$filter = $_GET['filter'] ?? 'all'; // 'all', 'now-showing', 'coming-soon'

// 2. Lấy tất cả phim
$all_movies = getAllMovies();
$filtered_movies = [];
$page_title = "Tất cả phim";

// 3. Lọc phim dựa trên $filter
if ($filter == 'now-showing') {
    $page_title = "Phim đang chiếu";
    foreach ($all_movies as $movie) {
        if ($movie['Status'] == 'Đang chiếu') $filtered_movies[] = $movie;
    }
} elseif ($filter == 'coming-soon') {
    $page_title = "Phim sắp chiếu";
    foreach ($all_movies as $movie) {
        if ($movie['Status'] == 'Sắp chiếu') $filtered_movies[] = $movie;
    }
} else {
    $filtered_movies = $all_movies;
}

// 4. Include Header
include 'partials/header.php';
?>

<title><?php echo $page_title; ?> - CinemaHub</title>

<main class="section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo $page_title; ?></h2>
            <div class="filter-tabs">
                <a href="movies.php?filter=all" class="<?php echo ($filter == 'all') ? 'active' : ''; ?>">Tất cả</a>
                <a href="movies.php?filter=now-showing" class="<?php echo ($filter == 'now-showing') ? 'active' : ''; ?>">Đang chiếu</a>
                <a href="movies.php?filter=coming-soon" class="<?php echo ($filter == 'coming-soon') ? 'active' : ''; ?>">Sắp chiếu</a>
            </div>
        </div>

        <div class="movie-grid">
            <?php if (empty($filtered_movies)): ?>
                <p class="empty-message">Không tìm thấy phim nào phù hợp.</p>
            <?php else: ?>
                <?php foreach ($filtered_movies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img src="<?php echo htmlspecialchars($movie['PosterURL'] ?? 'https://via.placeholder.com/400x600'); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                            <div class="movie-overlay">
                                <a href="movie-detail.php?id=<?php echo $movie['MovieID']; ?>" class="btn-detail">Chi tiết</a>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($movie['Title']); ?></h3>
                            <div class="movie-meta">
                                <span class="duration"><?php echo $movie['Duration']; ?> phút</span>
                            </div>
                            
                            <?php if ($movie['Status'] == 'Đang chiếu'): ?>
                                <a href="showtimes.php?movie_id=<?php echo $movie['MovieID']; ?>" class="btn-book-ticket">Đặt vé</a>
                            <?php else: ?>
                                <button class="btn-book-ticket" disabled>Chưa mở bán</button>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// 5. Include Footer
include 'partials/footer.php';
?>