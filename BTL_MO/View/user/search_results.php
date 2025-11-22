<?php
// BTL_MO/View/user/search_results.php

// 1. GỌI HÀM FUNCTIONS
require_once '../../functions/movies_functions.php';

// 2. LẤY TỪ KHÓA TÌM KIẾM TỪ URL
$query = $_GET['query'] ?? ''; // Lấy ?query=...
$search_results = [];

// 3. THỰC HIỆN TÌM KIẾM (nếu có $query)
if (!empty($query)) {
    $search_results = searchMoviesByName($query);
}

// 4. INCLUDE HEADER
$page_css = "home.css"; // Dùng chung CSS với trang chủ (vì có .movie-grid)
$page_title = "Kết quả tìm kiếm cho: " . htmlspecialchars($query);
include 'partials/header.php';
?>

<main class="section">
    <div class="container">
        
        <div class="section-header">
            <h2>Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($query); ?>"</h2>
        </div>

        <div class="movie-grid" id="searchResultsGrid">
            <?php if (empty($search_results)): ?>
                <p style="color: var(--text-secondary); font-size: 1.1em;">
                    Không tìm thấy phim nào khớp với từ khóa của bạn.
                </p>
            <?php else: ?>
                <?php foreach ($search_results as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img src="<?php echo htmlspecialchars($movie['PosterURL'] ?? 'https://via.placeholder.com/400x600'); ?>" alt="<?php echo htmlspecialchars($movie['Title']); ?>">
                            <div class="movie-overlay">
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
                                <button class="btn-book-ticket" disabled style="background: var(--bg-tertiary); cursor: not-allowed;">Chưa mở bán</button>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php
// 5. INCLUDE FOOTER
include 'partials/footer.php';
?>