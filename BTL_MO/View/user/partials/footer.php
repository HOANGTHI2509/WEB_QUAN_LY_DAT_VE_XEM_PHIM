<?php
// BTL_MO/View/user/partials/footer.php
?>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>Phim</h4>
                    <ul>
                        <li><a href="movies.php?filter=now-showing">Phim đang chiếu</a></li>
                        <li><a href="movies.php?filter=coming-soon">Phim sắp chiếu</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Rạp chiếu</h4>
                    <ul>
                        <li><a href="theaters.php">Tất cả rạp</a></li>
                    </ul>
                </div>
                </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> CinemaHub (BTL Mã Nguồn Mở). All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/all_effects.js"></script>
    
    <?php if (isset($page_js)): ?>
        <script src="../../assets/js/<?php echo $page_js; ?>"></script>
    <?php endif; ?>
    
</body>
</html>