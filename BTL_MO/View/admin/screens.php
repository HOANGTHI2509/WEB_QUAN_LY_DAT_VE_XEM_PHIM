<?php
// BTL_MO/View/admin/screens.php
include_once '../../functions/admin_gate.php';
include_once '../../functions/screens_functions.php';
include_once '../../functions/theaters_functions.php'; // Cần để lấy danh sách rạp cho dropdown

// 1. LẤY DỮ LIỆU CHUNG
$all_theaters = getAllTheaters();
$all_screens = getAllScreens(); // Hàm này lấy tất cả phòng kèm tên rạp

// 2. XỬ LÝ BỘ LỌC
$filter_theater_id = $_GET['theater_id'] ?? '';
$display_screens = [];

if ($filter_theater_id) {
    // Nếu có chọn rạp thì lọc ra
    foreach ($all_screens as $scr) {
        if ($scr['TheaterID'] == $filter_theater_id) {
            $display_screens[] = $scr;
        }
    }
} else {
    // Nếu không thì hiện tất cả
    $display_screens = $all_screens;
}

// 3. XỬ LÝ LOGIC SỬA (EDIT)
$action = $_GET['action'] ?? 'list';
$screen_focus = null;

if ($action == 'edit' && isset($_GET['id'])) {
    $screen_focus = getScreenById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phòng chiếu - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* CSS riêng cho bộ lọc */
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .filter-select { padding: 8px 12px; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: 4px; color: #fff; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Phòng chiếu</h1>
                <?php if ($action == 'list'): ?>
                    <button class="btn-add" onclick="openModal('addScreenModal')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Thêm phòng mới</span>
                    </button>
                <?php endif; ?>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thao tác thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <?php if ($action == 'edit' && $screen_focus): ?>
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Chỉnh sửa phòng chiếu</h3>
                            <a href="screens.php" class="btn-action">Quay lại</a>
                        </div>
                        <form action="../../Handle/screens_process.php" method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="screen_id" value="<?php echo $screen_focus['ScreenID']; ?>">
                            
                            <div class="form-group">
                                <label>Thuộc Rạp</label>
                                <select name="theater_id" required>
                                    <?php foreach ($all_theaters as $t): ?>
                                        <option value="<?php echo $t['TheaterID']; ?>" 
                                            <?php echo ($screen_focus['TheaterID'] == $t['TheaterID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($t['Name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tên phòng (VD: Phòng 1, IMAX)</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($screen_focus['Name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Sức chứa (Số ghế dự kiến)</label>
                                <input type="number" name="capacity" value="<?php echo $screen_focus['Capacity']; ?>" required>
                                <small style="color: #aaa;">Số ghế thực tế sẽ dựa trên sơ đồ bạn vẽ.</small>
                            </div>

                            <div class="modal-footer">
                                <a href="screens.php" class="btn-action">Hủy</a>
                                <button type="submit" class="btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <div class="dashboard-card">
                        <form method="GET" action="screens.php" class="filter-bar">
                            <select name="theater_id" class="filter-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả các rạp --</option>
                                <?php foreach ($all_theaters as $t): ?>
                                    <option value="<?php echo $t['TheaterID']; ?>" <?php echo ($filter_theater_id == $t['TheaterID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if($filter_theater_id): ?>
                                <a href="screens.php" class="btn-action">Xóa lọc</a>
                            <?php endif; ?>
                        </form>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Rạp</th>
                                        <th>Tên Phòng</th>
                                        <th>Sức chứa</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($display_screens)): ?>
                                        <tr><td colspan="5" class="text-center">Chưa có phòng chiếu nào.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($display_screens as $scr): ?>
                                        <tr>
                                            <td>#<?php echo $scr['ScreenID']; ?></td>
                                            <td><?php echo htmlspecialchars($scr['TheaterName']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($scr['Name']); ?></strong></td>
                                            <td><?php echo $scr['Capacity']; ?> ghế</td>
                                            <td>
                                                <a href="seats.php?screen_id=<?php echo $scr['ScreenID']; ?>" class="btn-action" style="color: #46d369; border-color: #46d369;">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><path d="M3 9h18"></path><path d="M9 21V9"></path></svg>
                                                    Cấu hình ghế
                                                </a>

                                                <a href="screens.php?action=edit&id=<?php echo $scr['ScreenID']; ?>" class="btn-action">Sửa</a>
                                                
                                                <form action="../../Handle/screens_process.php" method="POST" style="display:inline;" onsubmit="return confirm('CẢNH BÁO: Xóa phòng sẽ xóa luôn sơ đồ ghế của phòng này?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="screen_id" value="<?php echo $scr['ScreenID']; ?>">
                                                    <button class="btn-action danger">Xóa</button>
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

    <div id="addScreenModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h2>Thêm phòng chiếu</h2><button class="btn-close" onclick="closeModal('addScreenModal')">&times;</button></div>
            <form action="../../Handle/screens_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn Rạp</label>
                        <select name="theater_id" required>
                            <option value="">-- Chọn rạp --</option>
                            <?php foreach ($all_theaters as $t): ?>
                                <option value="<?php echo $t['TheaterID']; ?>" <?php echo ($filter_theater_id == $t['TheaterID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tên phòng</label>
                        <input type="text" name="name" placeholder="VD: Phòng 1, Phòng IMAX" required>
                    </div>
                    <div class="form-group">
                        <label>Sức chứa</label>
                        <input type="number" name="capacity" value="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addScreenModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>