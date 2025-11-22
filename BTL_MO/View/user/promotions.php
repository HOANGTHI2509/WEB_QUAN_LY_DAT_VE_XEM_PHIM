<?php
// BTL_MO/View/user/promotions.php

require_once '../../functions/promotions_functions.php';

$promotions_list = getAllPromotions();
$page_title = "Khuyến mãi";
$page_css = "home.css"; // Dùng chung CSS với trang chủ (vì có .promo-grid)

include 'partials/header.php';
?>

<title><?php echo $page_title; ?> - Cinema</title>

<main class="section">
    <div class="container">
        <div class="section-header">
            <h2>Khuyến mãi & Sự kiện</h2>
        </div>

        <div class="promo-grid">
            <?php if (empty($promotions_list)): ?>
                <p class="empty-message">Hiện chưa có khuyến mãi nào.</p>
            <?php else: ?>
                <?php foreach ($promotions_list as $promo): ?>
                    <div class="promo-card">
                        <img src="https://via.placeholder.com/400x200" alt="<?php echo htmlspecialchars($promo['Code']); ?>">
                        <div class="promo-content">
                            <span class="promo-badge">
                                <?php 
                                if (!empty($promo['DiscountPercent'])) {
                                    echo "Giảm " . $promo['DiscountPercent'] . "%";
                                } else {
                                    echo "Giảm " . number_format($promo['DiscountValue'], 0, ',', '.') . " ₫";
                                }
                                ?>
                            </span>
                            <h3>Mã: <?php echo htmlspecialchars($promo['Code']); ?></h3>
                            <p>Áp dụng từ <?php echo date('d/m/Y', strtotime($promo['StartDate'])); ?> 
                               đến <?php echo date('d/m/Y', strtotime($promo['EndDate'])); ?></p>
                            <a href="#" class="promo-link">Chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include 'partials/footer.php';
?>