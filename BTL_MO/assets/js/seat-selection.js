// BTL_MO/assets/js/seat-selection.js

// Biến toàn cục lưu ghế đang chọn
let selectedSeats = [];
const MAX_SEATS = 8; // Giới hạn số ghế được chọn
let countdownTime = 600; // 10 phút
let countdownInterval;

// Khởi chạy khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    startCountdown();
    updateSummary(); // Reset trạng thái ban đầu
});

/**
 * Xử lý khi click vào một ghế
 * (Hàm này được gọi từ sự kiện onclick trong thẻ HTML do PHP sinh ra)
 */
function toggleSeat(element) {
    // 1. Chặn click nếu ghế đã bán hoặc không khả dụng
    // (Dựa vào class CSS mà PHP đã gán)
    if (element.classList.contains('occupied') || 
        element.classList.contains('sold') || 
        element.classList.contains('held') || 
        element.classList.contains('reserved')) {
        return;
    }

    // 2. Lấy dữ liệu từ attributes
    const seatId = element.getAttribute('data-seat-id');
    const seatName = element.getAttribute('data-seat-name');
    // Lấy giá từ data-price (PHP đã tính sẵn giá gốc + phụ thu)
    const price = parseFloat(element.getAttribute('data-price'));

    // 3. Xử lý logic Chọn / Bỏ chọn
    if (element.classList.contains('selected')) {
        // Bỏ chọn
        element.classList.remove('selected');
        // Xóa khỏi mảng
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        // Chọn mới
        if (selectedSeats.length >= MAX_SEATS) {
            alert(`Bạn chỉ được chọn tối đa ${MAX_SEATS} ghế.`);
            return;
        }
        // Thêm class để đổi màu (Xanh lá)
        element.classList.add('selected');
        // Thêm vào mảng
        selectedSeats.push({ id: seatId, name: seatName, price: price });
    }

    // 4. Cập nhật giao diện bên phải (Tổng tiền, nút Tiếp tục)
    updateSummary();
}

/**
 * Cập nhật Sidebar (Danh sách ghế đã chọn & Tổng tiền)
 */
function updateSummary() {
    const container = document.getElementById('selectedSeats');
    const hiddenInputs = document.getElementById('hiddenInputs');
    const btnContinue = document.getElementById('btnContinue');
    const totalPriceEl = document.getElementById('totalPrice');

    // Xóa nội dung cũ
    container.innerHTML = '';
    hiddenInputs.innerHTML = '';

    // Nếu chưa chọn ghế nào
    if (selectedSeats.length === 0) {
        container.innerHTML = '<p class="empty-message">Chưa chọn ghế</p>';
        btnContinue.disabled = true; // Khóa nút
        btnContinue.style.opacity = '0.5';
        btnContinue.style.cursor = 'not-allowed';
        totalPriceEl.textContent = '0 ₫';
        return;
    }

    // Vẽ lại danh sách ghế đã chọn
    let total = 0;
    selectedSeats.forEach(seat => {
        // a. Hiển thị thẻ tag bên phải
        const tag = document.createElement('div');
        tag.className = 'seat-tag';
        // Style inline cho nhanh (hoặc dùng CSS .seat-tag)
        tag.style.cssText = "background: #333; padding: 5px 10px; border-radius: 15px; font-size: 13px; display: flex; align-items: center; gap: 5px; border: 1px solid #555; color: #fff; margin-bottom: 5px;";
        tag.innerHTML = `${seat.name} <span style="cursor:pointer; color:#ff4444; font-weight:bold; margin-left:5px;" onclick="removeSeat('${seat.id}')">×</span>`;
        container.appendChild(tag);

        // b. Tạo input ẩn để gửi Form đi (QUAN TRỌNG NHẤT)
        // Khi submit form, các input này sẽ được gửi sang trang tiếp theo
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'seat_ids[]'; // Mảng ID ghế
        input.value = seat.id;
        hiddenInputs.appendChild(input);

        total += seat.price;
    });

    // Cập nhật tổng tiền
    totalPriceEl.textContent = total.toLocaleString('vi-VN') + ' ₫';
    
    // Mở khóa nút tiếp tục
    btnContinue.disabled = false;
    btnContinue.style.opacity = '1';
    btnContinue.style.cursor = 'pointer';
}

/**
 * Xóa ghế khi nhấn vào dấu X ở sidebar
 */
function removeSeat(seatId) {
    // Tìm ghế trên màn hình (bằng data attribute) và bỏ class selected
    const seatEl = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
    if (seatEl) {
        seatEl.classList.remove('selected');
    }
    // Xóa khỏi mảng dữ liệu
    selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    updateSummary();
}

/**
 * Đếm ngược thời gian giữ ghế
 */
function startCountdown() {
    const display = document.getElementById('countdown');
    if (!display) return;

    countdownInterval = setInterval(() => {
        let m = Math.floor(countdownTime / 60);
        let s = countdownTime % 60;
        // Thêm số 0 đằng trước nếu nhỏ hơn 10
        display.textContent = `${m < 10 ? '0'+m : m}:${s < 10 ? '0'+s : s}`;

        if (--countdownTime < 0) {
            clearInterval(countdownInterval);
            alert("Hết thời gian giữ ghế. Trang sẽ tải lại.");
            window.location.reload();
        }
    }, 1000);
}