/*
=========================================================
FILE HIỆU ỨNG CHUNG (UI)
Chứa các hàm cho:
- Mở/Đóng Modal
- Slider trang chủ
- Hiện/Ẩn mật khẩu
- Nút Đăng xuất
- Thông báo (nếu cần)
=========================================================
*/

document.addEventListener('DOMContentLoaded', function() {
    
    // --- LOGIC Mobile Menu ---
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            if (navLinks) {
                navLinks.classList.toggle('active');
            }
            this.classList.toggle('active');
        });
    }

    // --- LOGIC Slider Trang chủ ---
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;

    function showSlide(index) {
        if (slides.length === 0) return; 
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        if (index >= slides.length) currentSlide = 0;
        if (index < 0) currentSlide = slides.length - 1;
        if (slides[currentSlide]) {
            slides[currentSlide].classList.add('active');
        }
        if (dots[currentSlide]) {
            dots[currentSlide].classList.add('active');
        }
    }

    if (slides.length > 0 && dots.length > 0) {
        function nextSlide() {
            currentSlide++;
            showSlide(currentSlide);
        }
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
        setInterval(nextSlide, 5000); 
    }

}); // --- Kết thúc DOMContentLoaded ---


// =========================================================
// CÁC HÀM TIỆN ÍCH GIAO DIỆN
// =========================================================

/**
 * Mở một modal (cửa sổ pop-up)
 * @param {string} modalId - ID của modal (ví dụ: 'addMovieModal')
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

/**
 * Đóng một modal
 * @param {string} modalId - ID của modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Thêm sự kiện để đóng modal khi nhấn ra ngoài
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

/**
 * Hiển thị/Ẩn mật khẩu
 * @param {string} inputId - ID của input mật khẩu
 */
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    if (passwordInput) {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
    }
}

/**
 * Hiển thị thông báo
 * (Dùng để hiển thị các thông báo từ URL, ví dụ: ?success=Them_thanh_cong)
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${type === 'success' ? '<polyline points="20 6 9 17 4 12"></polyline>' : '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>'}
            </svg>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Chuyển hướng đến trang đăng xuất
 */
function logout() {
    let path = '../Handle/logout_process.php';
    // Kiểm tra xem có đang ở trong thư mục /admin/ không
    if (window.location.pathname.includes('/admin/')) {
         path = '../../Handle/logout_process.php';
    }
    window.location.href = path;
}