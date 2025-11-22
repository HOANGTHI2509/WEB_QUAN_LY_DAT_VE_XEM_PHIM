// BTL_MO/assets/js/food-selection.js

let selectedFoods = {}; // Object lưu: { id: {qty, price, name} }

function updateFood(btn, change) {
    // 1. Lấy thông tin món ăn từ HTML
    const controlDiv = btn.closest('.qty-control');
    const id = controlDiv.getAttribute('data-id');
    const price = parseFloat(controlDiv.getAttribute('data-price'));
    const name = controlDiv.getAttribute('data-name');
    const display = controlDiv.querySelector('.qty-val');

    // 2. Tính số lượng mới
    let currentQty = selectedFoods[id] ? selectedFoods[id].qty : 0;
    let newQty = currentQty + change;

    if (newQty < 0) newQty = 0;

    // 3. Cập nhật Object dữ liệu
    if (newQty === 0) {
        delete selectedFoods[id];
        // Reset màu hiển thị
        display.style.color = '#fff';
    } else {
        selectedFoods[id] = {
            qty: newQty,
            price: price,
            name: name
        };
        // Highlight số lượng
        display.style.color = '#e50914'; 
    }

    // 4. Cập nhật số trên giao diện
    display.textContent = newQty;

    // 5. Tính lại tổng tiền và vẽ lại Sidebar
    renderSummary();
}

function renderSummary() {
    const foodListContainer = document.getElementById('selectedFoodList');
    const foodContainerBlock = document.getElementById('selectedFoodContainer');
    const foodTotalDisplay = document.getElementById('foodTotalDisplay');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    const foodInputsDiv = document.getElementById('foodInputs');

    let foodTotal = 0;
    let htmlList = '';
    let htmlInputs = '';

    // Duyệt qua danh sách món đã chọn
    for (const [id, item] of Object.entries(selectedFoods)) {
        const itemTotal = item.qty * item.price;
        foodTotal += itemTotal;

        // HTML hiển thị bên Sidebar
        htmlList += `
            <div class="info-row" style="font-size: 13px; margin-bottom: 5px; border-bottom: 1px dashed #333; padding-bottom: 5px;">
                <span style="color: #fff;">${item.name} <strong style="color: #e50914;">x${item.qty}</strong></span>
                <span>${itemTotal.toLocaleString('vi-VN')} ₫</span>
            </div>
        `;

        // Input ẩn để gửi Form (foods[1]=2)
        htmlInputs += `<input type="hidden" name="foods[${id}]" value="${item.qty}">`;
    }

    // Ẩn hiện khối Food Summary
    if (foodTotal > 0) {
        foodContainerBlock.style.display = 'block';
        foodListContainer.innerHTML = htmlList;
    } else {
        foodContainerBlock.style.display = 'none';
        foodListContainer.innerHTML = '';
    }

    // Cập nhật tiền
    foodTotalDisplay.textContent = foodTotal.toLocaleString('vi-VN') + ' ₫';
    
    // seatTotal là biến toàn cục được PHP in ra ở cuối file HTML
    const grandTotal = seatTotal + foodTotal;
    grandTotalDisplay.textContent = grandTotal.toLocaleString('vi-VN') + ' ₫';

    // Cập nhật Input ẩn
    foodInputsDiv.innerHTML = htmlInputs;
}