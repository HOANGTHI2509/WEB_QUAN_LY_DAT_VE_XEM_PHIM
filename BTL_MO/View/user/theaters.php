<?php
// BTL_MO/View/user/theaters.php

require_once '../../functions/theaters_functions.php';

// 1. Lấy tất cả rạp
$theaters_list = getAllTheaters();
$page_title = "Danh sách rạp";

// 2. Include Header
include 'partials/header.php';
?>

<title><?php echo $page_title; ?> - CinemaHub</title>

<main class="section">
    <div class="container">
        <div class="section-header">
            <h2>Hệ thống rạp CinemaHub</h2>
        </div>

        <div class="theater-list">
            <?php if (empty($theaters_list)): ?>
                <p class="empty-message">Hệ thống chưa có rạp nào.</p>
            <?php else: ?>
                <?php foreach ($theaters_list as $theater): ?>
                    <div class="theater-card">
                        <h3><?php echo htmlspecialchars($theater['Name']); ?></h3>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($theater['Address']); ?>, <?php echo htmlspecialchars($theater['City']); ?></p>
                        <p><strong>Số phòng:</strong> <?php echo $theater['TotalScreens']; ?></p>
                        <a href="showtimes.php?theater_id=<?php echo $theater['TheaterID']; ?>" class="btn-primary">Xem lịch chiếu</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    .theater-list { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .theater-card { background: var(--bg-secondary); padding: 20px; border-radius: var(--border-radius); }
    .theater-card h3 { color: var(--primary-color); margin-bottom: 10px; }
    .theater-card p { color: var(--text-secondary); margin-bottom: 15px; }
    @media (max-width: 768px) { .theater-list { grid-template-columns: 1fr; } }
</style>

<?php
// 3. Include Footer
include 'partials/footer.php';
?>