<?php
// BTL_MO/View/admin/theaters.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/theaters_functions.php';

$action = $_GET['action'] ?? 'list'; 
$theater_to_edit = null; 

if ($action == 'edit' && isset($_GET['id'])) {
    $theater_to_edit = getTheaterById((int)$_GET['id']);
}

if ($action == 'add') {
    $theater_to_edit = [
        'TheaterID' => '', 'Name' => '', 'Address' => '', 
        'City' => '', 'Phone' => '', 'Email' => ''
    ];
}

$theaters_list = [];
if ($action == 'list') {
    $theaters_list = getAllTheaters();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý rạp - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý rạp chiếu</h1>
                 <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addTheaterModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Thêm rạp mới</span>
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
                        if ($_GET['success'] == 'add') echo "Thêm rạp mới thành công!";
                        if ($_GET['success'] == 'update') echo "Cập nhật rạp thành công!";
                        if ($_GET['success'] == 'delete') echo "Xóa rạp thành công!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($action == 'edit' || $action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo ($action == 'edit') ? 'Chỉnh sửa rạp' : 'Thêm rạp mới'; ?></h3>
                    </div>
                    
                    <form action="../../Handle/theaters_process.php" method="POST">
                        
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="TheaterID" value="<?php echo $theater_to_edit['TheaterID']; ?>">
                        <?php else: ?>
                            <?php endif; ?>

                        <div class="form-group">
                            <label>Tên rạp</label>
                            <input type="text" name="Name" value="<?php echo htmlspecialchars($theater_to_edit['Name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="Address" value="<?php echo htmlspecialchars($theater_to_edit['Address']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Thành phố</label>
                            <input type="text" name="City" value="<?php echo htmlspecialchars($theater_to_edit['City']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="Phone" value="<?php echo htmlspecialchars($theater_to_edit['Phone']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="Email" value="<?php echo htmlspecialchars($theater_to_edit['Email']); ?>">
                        </div>

                        <div class="modal-footer">
                            <a href="theaters.php" class="btn-action">Hủy</a>
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
                                    <th>Tên rạp</th>
                                    <th>Địa chỉ</th>
                                    <th>Thành phố</th>
                                    <th>Số phòng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="theatersTableBody">
                                <?php if (empty($theaters_list)): ?>
                                    <tr><td colspan="6" class="text-center">Chưa có rạp nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($theaters_list as $theater): ?>
                                    <tr>
                                        <td><strong>#<?php echo $theater['TheaterID']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($theater['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($theater['Address']); ?></td>
                                        <td><?php echo htmlspecialchars($theater['City']); ?></td>
                                        <td><?php echo $theater['TotalScreens']; ?></td>
                                        <td>
                                            <a href="theaters.php?action=edit&id=<?php echo $theater['TheaterID']; ?>" class="btn-action">
                                                Sửa
                                            </a>
                                            <form action="../../Handle/theaters_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('CẢNH BÁO: Xóa rạp sẽ xóa HẾT phòng chiếu và ghế của rạp đó. Bạn có chắc chắn?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="TheaterID" value="<?php echo $theater['TheaterID']; ?>">
                                                <button type="submit" class="btn-action danger">Xóa</button>
                                            </form>
                                            <a href="screens.php?theater_id=<?php echo $theater['TheaterID']; ?>" class="btn-action">
                                                Quản lý phòng
                                            </a>
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

    <div id="addTheaterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm rạp mới</h2>
                <button class="btn-close" onclick="closeModal('addTheaterModal')">&times;</button>
            </div>
            <form id="addTheaterForm" action="../../Handle/theaters_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên rạp</label>
                        <input type="text" name="Name" required>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" name="Address" required>
                    </div>
                    <div class="form-group">
                        <label>Thành phố</label>
                        <input type="text" name="City" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="Phone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="Email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addTheaterModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm rạp</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>