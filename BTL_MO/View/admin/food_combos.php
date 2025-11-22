<?php
// BTL_MO/View/admin/food_combos.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/food_functions.php';

$action = $_GET['action'] ?? 'list'; 
$item_to_edit = null; 

if ($action == 'edit' && isset($_GET['id'])) {
    $item_to_edit = getFoodComboById((int)$_GET['id']);
}

// Nếu là thêm mới, khởi tạo mảng rỗng để tránh lỗi
if ($action == 'add') {
    $item_to_edit = [
        'FoodID' => '', 'Name' => '', 'Description' => '', 
        'Price' => '', 'ImageURL' => ''
    ];
}

$food_list = [];
if ($action == 'list') {
    $food_list = getAllFoodCombos();
}

// Hàm hỗ trợ hiển thị ảnh
function getFoodImage($url) {
    if (empty($url)) return 'https://via.placeholder.com/150x150?text=No+Image';
    // Nếu là link online (http) thì giữ nguyên, nếu là file upload thì thêm đường dẫn gốc
    if (strpos($url, 'http') === 0) return htmlspecialchars($url);
    return "../../" . htmlspecialchars($url);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đồ ăn - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .preview-img {
            width: 100px; height: 100px; 
            object-fit: cover; border-radius: 8px; 
            margin-top: 10px; border: 1px solid #444;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Đồ ăn & Combo</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addFoodModal')">
                            <span>+ Thêm món mới</span>
                        </button>
                    <?php endif; ?>
                    <div class="user-menu">
                        <img src="../../assets/images/default-avatar.png" alt="Admin">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Thao tác thành công!</div>
                <?php endif; ?>

                <?php if ($action == 'edit' || $action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo ($action == 'edit') ? 'Chỉnh sửa món' : 'Thêm món mới'; ?></h3>
                    </div>
                    
                    <form action="../../Handle/food_process.php" method="POST" enctype="multipart/form-data">
                        
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="food_id" value="<?php echo $item_to_edit['FoodID']; ?>">
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($item_to_edit['ImageURL']); ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Tên món / combo</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($item_to_edit['Name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea name="description" rows="3"><?php echo htmlspecialchars($item_to_edit['Description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Giá (VNĐ)</label>
                            <input type="number" name="price" value="<?php echo htmlspecialchars($item_to_edit['Price']); ?>" required step="1000" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label>Hình ảnh</label>
                            <input type="file" name="image_file" accept="image/*" class="custom-input">
                            <small style="color: #aaa;">Chấp nhận: JPG, PNG, GIF. Để trống nếu không muốn thay đổi ảnh.</small>
                            
                            <?php if (!empty($item_to_edit['ImageURL'])): ?>
                                <div>
                                    <img src="<?php echo getFoodImage($item_to_edit['ImageURL']); ?>" class="preview-img">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="modal-footer">
                            <a href="food_combos.php" class="btn-action">Hủy</a>
                            <button type="submit" class="btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>

                <?php else: ?>

                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên món / combo</th>
                                    <th>Giá</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($food_list)): ?>
                                    <tr><td colspan="5" class="text-center">Chưa có món nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($food_list as $item): ?>
                                    <tr>
                                        <td><strong>#<?php echo $item['FoodID']; ?></strong></td>
                                        <td>
                                            <img src="<?php echo getFoodImage($item['ImageURL']); ?>"
                                                 alt="<?php echo htmlspecialchars($item['Name']); ?>"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #444;">
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($item['Name']); ?></strong></td>
                                        <td style="color: var(--primary-color); font-weight: bold;">
                                            <?php echo number_format($item['Price'], 0, ',', '.'); ?> ₫
                                        </td>
                                        <td>
                                            <a href="food_combos.php?action=edit&id=<?php echo $item['FoodID']; ?>" class="btn-action">Sửa</a>
                                            <form action="../../Handle/food_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa món này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="food_id" value="<?php echo $item['FoodID']; ?>">
                                                <button type="submit" class="btn-action danger">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div id="addFoodModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm món mới</h2>
                <button class="btn-close" onclick="closeModal('addFoodModal')">&times;</button>
            </div>
            <form id="addFoodForm" action="../../Handle/food_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                     <div class="form-group">
                        <label>Tên món / combo</label>
                        <input type="text" name="name" required class="custom-input">
                    </div>
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" rows="3" class="custom-input"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Giá (VNĐ)</label>
                        <input type="number" name="price" required step="1000" min="0" class="custom-input">
                    </div>
                    
                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="image_file" accept="image/*" class="custom-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addFoodModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm món</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>