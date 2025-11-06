/*
=========================================================
FILE HIỆU ỨNG TRANG CHỌN GHẾ
- Xử lý UI: Chọn/bỏ chọn ghế
- Tính tổng tiền
- Đếm ngược
- Cập nhật các input ẩn của Form
=========================================================
*/

let selectedSeats = [];
let countdownTime = 600; // 10 phút
let countdownInterval;

/*
QUAN TRỌNG:
Các biến sau đây phải được PHP định nghĩa TRƯỚC KHI file này được gọi
(thường là trong <head> hoặc ngay trước thẻ <script> này)

<script>
  const showtimeId = <?php echo $showtime_id; ?>;
  const seatPrices = {
    normal: <?php echo $gia_ve_thuong; ?>,
    vip: <?php echo $gia_ve_vip; ?>
  };
</script>

*/

/**
 * Hàm được gọi khi nhấn vào một ghế
 * (Ghế này đã được PHP render ra với onclick="toggleSeat(this)")
 */
function toggleSeat(element) {
    if (element.classList.contains('occupied')) {
        return; // Không cho chọn ghế đã đặt
    }

    const seatId = element.getAttribute('data-seat-id');
    const seatName = element.getAttribute('data-seat-name');
    const seatType = element.getAttribute('data-seat-type');
    const price = seatType === 'VIP' ? seatPrices.vip : seatPrices.normal;

    if (element.classList.contains('selected')) {
        // Bỏ chọn
        element.classList.remove('selected');
        selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    } else {
        // Chọn
        if (selectedSeats.length >= 10) {
            alert('Chỉ được chọn tối đa 10 ghế');
            return;
        }
        element.classList.add('selected');
        selectedSeats.push({
            id: seatId,
            name: seatName,
            type: seatType,
            price: price
        });
    }

    updateSummary();
    startCountdown();
}

/**
 * Cập nhật tóm tắt đơn hàng (sidebar)
 * và Cập nhật các input ẩn cho form
 */
function updateSummary() {
    const selectedSeatsContainer = document.getElementById('selectedSeats');
    const btnContinue = document.getElementById('btnContinue');
    const formInputContainer = document.getElementById('seatFormInputContainer'); // Vùng chứa input ẩn

    if (selectedSeats.length === 0) {
        selectedSeatsContainer.innerHTML = '<p class="empty-message">Chưa chọn ghế</p>';
        btnContinue.disabled = true;
        formInputContainer.innerHTML = ''; // Xóa input
    } else {
        // Cập nhật thẻ tag (hiển thị)
        selectedSeatsContainer.innerHTML = selectedSeats.map(seat => `
            <div class="seat-tag ${seat.type === 'VIP' ? 'vip' : ''}">
                ${seat.name}
                <button type="button" onclick="removeSeat('${seat.id}')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        `).join('');
        
        // Cập nhật input ẩn (để form PHP gửi đi)
        formInputContainer.innerHTML = selectedSeats.map(seat => 
            `<input type="hidden" name="seat_ids[]" value="${seat.id}">`
        ).join('');

        btnContinue.disabled = false;
    }

    // Cập nhật giá
    const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
    document.getElementById('seatsTotal').textContent = formatCurrency(total);
    document.getElementById('totalPrice').textContent = formatCurrency(total);
}

/**
 * Xóa ghế khỏi danh sách đã chọn (khi nhấn nút X trên tag)
 */
function removeSeat(seatId) {
    const seatElement = document.querySelector(`[data-seat-id="${seatId}"]`);
    if (seatElement) {
        seatElement.classList.remove('selected');
    }
    selectedSeats = selectedSeats.filter(s => s.id !== seatId);
    updateSummary();
}

/**
 * Bắt đầu đếm ngược 10 phút
 */
function startCountdown() {
    if (countdownInterval) {
        return; // Đã chạy rồi, không chạy lại
    }

    countdownInterval = setInterval(() => {
        countdownTime--;

        const minutes = Math.floor(countdownTime / 60);
        const seconds = countdownTime % 60;
        document.getElementById('countdown').textContent =
            `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        if (countdownTime <= 0) {
            clearInterval(countdownInterval);
            alert('Đã hết thời gian giữ ghế. Trang sẽ được tải lại.');
            window.location.reload(); // Tải lại trang
        }
    }, 1000);
}

// Hàm tiện ích (chỉ dùng cho file này)
function formatCurrency(value) {
    if (typeof value !== 'number') value = 0;
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
}

// Tự động kích hoạt khi trang tải
document.addEventListener('DOMContentLoaded', () => {
    // Nếu có lỗi (ví dụ: ?error=het_thoi_gian), PHP có thể in ra thông báo
    // Bằng cách gọi hàm showNotification() từ all_effects.js
});