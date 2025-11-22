<?php
// BTL_MO/View/user/partials/footer.php
?>
    <style>
        .footer {
            background-color: #0f1014; /* Nền đen đậm */
            color: #fff;
            padding: 60px 0 30px;
            border-top: 1px solid #222;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr; /* Cột đầu rộng hơn */
            gap: 40px;
            margin-bottom: 40px;
        }

        /* Cột 1: Logo & Mô tả */
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 800;
            color: #e50914; /* Màu đỏ thương hiệu */
            margin-bottom: 15px;
            text-decoration: none;
        }
        .footer-desc {
            color: #a0a0a0;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            max-width: 300px;
        }
        .social-links {
            display: flex;
            gap: 12px;
        }
        .social-btn {
            width: 36px;
            height: 36px;
            background: #222;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: 0.3s;
            text-decoration: none;
        }
        .social-btn:hover {
            background: #e50914;
            transform: translateY(-3px);
        }

        /* Các cột liên kết */
        .footer-col h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }
        .footer-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-col ul li {
            margin-bottom: 12px;
        }
        .footer-col ul li a {
            color: #a0a0a0;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }
        .footer-col ul li a:hover {
            color: #e50914;
            padding-left: 5px; /* Hiệu ứng đẩy nhẹ sang phải */
        }

        /* Dòng bản quyền cuối */
        .footer-bottom {
            border-top: 1px solid #222;
            padding-top: 20px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-content { grid-template-columns: 1fr 1fr; } /* 2 cột trên tablet */
        }
        @media (max-width: 768px) {
            .footer-content { grid-template-columns: 1fr; text-align: center; } /* 1 cột trên mobile */
            .footer-logo, .social-links { justify-content: center; }
            .footer-desc { margin: 0 auto 20px; }
        }
    </style>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                
                <div class="footer-col">
                    <a href="index.php" class="footer-logo">
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
                    <p class="footer-desc">
                        Hệ thống rạp chiếu phim hiện đại, mang đến trải nghiệm điện ảnh tuyệt vời nhất cho mọi tín đồ phim ảnh.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                        <a href="#" class="social-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-3.78-1.54c-2.5 0-4.53 2.04-4.53 4.54 0 0.39 0.04 0.78 0.12 1.15-3.77-0.2-7.12-2-9.36-4.87a4.48 4.48 0 0 0-.6 2.27c0 1.57 0.8 2.95 2 3.76-0.74-0.02-1.44-0.23-2.05-0.57v0.06c0 2.2 1.56 4.03 3.64 4.44-0.38 0.1-0.78 0.16-1.2 0.16-0.29 0-0.58-0.02-0.86-0.08 0.58 1.8 2.26 3.11 4.25 3.15-1.55 1.21-3.5 1.93-5.62 1.93-0.36 0-0.72-0.02-1.07-0.05 2 1.29 4.39 2.04 6.94 2.04 8.32 0 12.87-6.9 12.87-12.87 0-0.2 0-0.39-0.03-0.59 0.88-0.64 1.65-1.44 2.25-2.36z"></path></svg>
                        </a>
                        <a href="#" class="social-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Phim</h4>
                    <ul>
                        <li><a href="movies.php?filter=now-showing">Phim đang chiếu</a></li>
                        <li><a href="movies.php?filter=coming-soon">Phim sắp chiếu</a></li>
                        <li><a href="#">Phim IMAX</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Rạp chiếu</h4>
                    <ul>
                        <li><a href="theaters.php">Tất cả rạp</a></li>
                        <li><a href="#">Rạp đặc biệt</a></li>
                        <li><a href="#">Giá vé</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Chính sách & Quy định</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Cinema -  sản phẩm môn học mã nguồn mở.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/all_effects.js"></script>
    
    <?php if (isset($page_js)): ?>
        <script src="../../assets/js/<?php echo $page_js; ?>"></script>
    <?php endif; ?>
    
</body>
</html>