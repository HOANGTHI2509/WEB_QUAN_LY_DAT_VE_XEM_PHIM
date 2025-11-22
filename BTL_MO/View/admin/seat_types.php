<?php
// BTL_MO/View/admin/seat_types.php
include_once '../../functions/admin_gate.php';
include_once '../../functions/seattypes_functions.php';

// 1. XỬ LÝ LOGIC
$action = $_GET['action'] ?? 'list';
$seat_types = getAllSeatTypes();
$type_focus = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $type_focus = getSeatTypeById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Loại ghế - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Loại ghế & Giá vé</h1>
                <?php if ($action == 'list'): ?>
                    <button class="btn-add" onclick="openModal('addSeatTypeModal')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Thêm loại ghế</span>
                    </button>
                <?php endif; ?>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <?php if ($action == 'edit' && $type_focus): ?>
                    <div class="dashboard-card">
                        <h3>Chỉnh sửa loại ghế</h3>
                        <form action="../../Handle/seattypes_process.php" method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="seat_type_id" value="<?php echo $type_focus['SeatTypeID']; ?>">
                            
                            <div class="form-group">
                                <label>Tên loại ghế (VD: VIP, Sweetbox)</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($type_focus['Name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phụ thu giá vé (VNĐ)</label>
                                <input type="number" name="price_surcharge" value="<?php echo $type_focus['PriceSurcharge']; ?>" required step="1000" min="0">
                                <small style="color: #aaa;">Giá này sẽ cộng thêm vào giá gốc của suất chiếu.</small>
                            </div>

                            <div class="modal-footer">
                                <a href="seat_types.php" class="btn-action">Hủy</a>
                                <button type="submit" class="btn-primary">Lưu thay đổi</button>
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
                                        <th>Tên loại ghế</th>
                                        <th>Phụ thu (VNĐ)</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seat_types as $type): ?>
                                    <tr>
                                        <td>#<?php echo $type['SeatTypeID']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($type['Name']); ?></strong></td>
                                        <td style="color: var(--success-color); font-weight: bold;">
                                            +<?php echo number_format($type['PriceSurcharge'], 0, ',', '.'); ?> ₫
                                        </td>
                                        <td>
                                            <a href="seat_types.php?action=edit&id=<?php echo $type['SeatTypeID']; ?>" class="btn-action">Sửa</a>
                                            <form action="../../Handle/seattypes_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Xóa loại ghế này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="seat_type_id" value="<?php echo $type['SeatTypeID']; ?>">
                                                <button class="btn-action danger">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div id="addSeatTypeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h2>Thêm loại ghế</h2><button class="btn-close" onclick="closeModal('addSeatTypeModal')">&times;</button></div>
            <form action="../../Handle/seattypes_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên loại ghế</label>
                        <input type="text" name="name" placeholder="VD: Ghế đôi" required>
                    </div>
                    <div class="form-group">
                        <label>Phụ thu (VNĐ)</label>
                        <input type="number" name="price_surcharge" value="0" required step="1000" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addSeatTypeModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>