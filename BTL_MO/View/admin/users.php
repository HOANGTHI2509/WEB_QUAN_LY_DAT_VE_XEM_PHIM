<?php
// BTL_MO/View/admin/users.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/users_functions.php';

$action = $_GET['action'] ?? 'list'; 
$user_to_edit = null; 

if ($action == 'edit' && isset($_GET['id'])) {
    $user_to_edit = getUserById((int)$_GET['id']);
}

if ($action == 'add') {
    $user_to_edit = [
        'UserID' => '', 'FullName' => '', 'Email' => '', 
        'PhoneNumber' => '', 'Role' => 'User'
    ];
}

$users_list = [];
if ($action == 'list') {
    $users_list = getAllUsers();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý người dùng</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addUserModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Thêm người dùng</span>
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
                        if ($_GET['success'] == 'add') echo "Thêm người dùng mới thành công!";
                        if ($_GET['success'] == 'update') echo "Cập nhật người dùng thành công!";
                        if ($_GET['success'] == 'delete') echo "Xóa người dùng thành công!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($action == 'edit' || $action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo ($action == 'edit') ? 'Chỉnh sửa người dùng' : 'Thêm người dùng'; ?></h3>
                    </div>
                    
                    <form action="../../Handle/users_process.php" method="POST">
                        
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="UserID" value="<?php echo $user_to_edit['UserID']; ?>">
                        <?php else: ?>
                            <?php endif; ?>

                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text" name="FullName" value="<?php echo htmlspecialchars($user_to_edit['FullName']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="Email" value="<?php echo htmlspecialchars($user_to_edit['Email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="PhoneNumber" value="<?php echo htmlspecialchars($user_to_edit['PhoneNumber']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Vai trò</label>
                            <select name="Role" required>
                                <option value="User" <?php echo ($user_to_edit['Role'] == 'User') ? 'selected' : ''; ?>>User</option>
                                <option value="Admin" <?php echo ($user_to_edit['Role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu</label>
                            <input type="password" name="Password" 
                                   placeholder="Bỏ trống để giữ nguyên mật khẩu"
                                   minlength="6">
                        </div>

                        <div class="modal-footer">
                            <a href="users.php" class="btn-action">Hủy</a>
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
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Vai trò</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php if (empty($users_list)): ?>
                                    <tr><td colspan="6" class="text-center">Chưa có người dùng nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users_list as $user): ?>
                                    <tr>
                                        <td><strong>#<?php echo $user['UserID']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['FullName']); ?></td>
                                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['PhoneNumber'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($user['Role'] == 'Admin') ? 'badge-warning' : 'badge-success'; ?>">
                                                <?php echo htmlspecialchars($user['Role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="users.php?action=edit&id=<?php echo $user['UserID']; ?>" class="btn-action">
                                                Sửa
                                            </a>
                                            
                                            <?php if ($user['UserID'] != 1 && $user['UserID'] != $_SESSION['user_id']): ?>
                                            <form action="../../Handle/users_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="UserID" value="<?php echo $user['UserID']; ?>">
                                                <button type="submit" class="btn-action danger">Xóa</button>
                                            </form>
                                            <?php endif; ?>
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

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm người dùng mới</h2>
                <button class="btn-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            <form id="addUserForm" action="../../Handle/users_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="FullName" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="Email" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="PhoneNumber">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="Password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <select name="Role" required>
                            <option value="User" selected>User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addUserModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm người dùng</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>