<?php
// BTL_MO/View/admin/promotions.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/promotions_functions.php';

$action = $_GET['action'] ?? 'list'; 
$item_to_edit = null; 

if ($action == 'edit' && isset($_GET['id'])) {
    $item_to_edit = getPromotionById((int)$_GET['id']);
}

if ($action == 'add') {
    $item_to_edit = [
        'PromotionID' => '', 'Code' => '', 'DiscountValue' => 0, 
        'DiscountPercent' => 0, 'StartDate' => date('Y-m-d'), 'EndDate' => date('Y-m-d')
    ];
}

$promo_list = [];
if ($action == 'list') {
    $promo_list = getAllPromotions();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khuyến mãi - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Khuyến mãi</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addPromoModal')">
                            <svg width="20" height="20"...></svg>
                            <span>Thêm mã mới</span>
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
                    <div class="alert alert-success">
                        <?php
                        if ($_GET['success'] == 'add') echo "Thêm khuyến mãi thành công!";
                        if ($_GET['success'] == 'update') echo "Cập nhật khuyến mãi thành công!";
                        if ($_GET['success'] == 'delete') echo "Xóa khuyến mãi thành công!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($action == 'edit' || $action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo ($action == 'edit') ? 'Chỉnh sửa khuyến mãi' : 'Thêm khuyến mãi'; ?></h3>
                    </div>
                    
                    <form action="../../Handle/promotions_process.php" method="POST">
                        
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="promotion_id" value="<?php echo $item_to_edit['PromotionID']; ?>">
                        <?php else: ?>
                            <?php endif; ?>

                        <div class="form-group">
                            <label>Mã Code</label>
                            <input type="text" name="code" value="<?php echo htmlspecialchars($item_to_edit['Code']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Giảm % (Bắt buộc)</label>
                            <input type="number" name="discount_percent" value="<?php echo $item_to_edit['DiscountPercent'] ?? 0; ?>" min="0" max="100" required>
                            <small style="color: #aaa;">Nhập số phần trăm muốn giảm (Ví dụ: 10).</small>
                        </div>

                        <div class="form-group">
                            <label>Mức giảm tối đa (VNĐ)</label>
                            <input type="number" name="discount_value" value="<?php echo $item_to_edit['DiscountValue'] ?? 0; ?>" min="0" step="1000">
                            <small style="color: #aaa;">Ví dụ: Nếu nhập 25000, đơn hàng dù lớn đến mấy cũng chỉ giảm tối đa 25k.</small>
                        </div>
                        <div class="form-group">
                            <label>Ngày bắt đầu</label>
                            <input type="date" name="start_date" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($item_to_edit['StartDate']))); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày kết thúc</label>
                            <input type="date" name="end_date" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($item_to_edit['EndDate']))); ?>" required>
                        </div>

                        <div class="modal-footer">
                            <a href="promotions.php" class="btn-action">Hủy</a>
                            <button type="submit" class="btn-primary">
                                Cập nhật
                            </button>
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
                                    <th>Mã Code</th>
                                    <th>Giá trị giảm</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($promo_list)): ?>
                                    <tr><td colspan="6" class="text-center">Chưa có khuyến mãi nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($promo_list as $item): ?>
                                    <tr>
                                        <td><strong>#<?php echo $item['PromotionID']; ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($item['Code']); ?></strong></td>
                                        <td>
                                            <?php 
                                            if (!empty($item['DiscountPercent'])) {
                                                echo $item['DiscountPercent'] . "%";
                                            } else {
                                                echo number_format($item['DiscountValue'], 0, ',', '.') . " ₫";
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($item['StartDate'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($item['EndDate'])); ?></td>
                                        <td>
                                            <a href="promotions.php?action=edit&id=<?php echo $item['PromotionID']; ?>" class="btn-action">
                                                Sửa
                                            </a>
                                            <form action="../../Handle/promotions_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="promotion_id" value="<?php echo $item['PromotionID']; ?>">
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

    <div id="addPromoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm khuyến mãi mới</h2>
                <button class="btn-close" onclick="closeModal('addPromoModal')">&times;</button>
            </div>
            <form id="addPromoForm" action="../../Handle/promotions_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                     <div class="form-group">
                        <label>Mã Code</label>
                        <input type="text" name="code" required>
                    </div>
                    <div class="form-group">
                        <label>Giảm % (VD: 10 cho 10%)</label>
                        <input type="number" name="discount_percent" value="0" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label>Giảm tiền (VNĐ)</label>
                        <input type="number" name="discount_value" value="0" min="0" step="1000">
                    </div>
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="start_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="end_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addPromoModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>