<?php
// BTL_MO/View/admin/genres.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/genres_functions.php';

$action = $_GET['action'] ?? 'list';
$genres = getAllGenres();
$genre_focus = null;

// Nếu đang sửa, lấy thông tin cũ
if ($action == 'edit' && isset($_GET['id'])) {
    $genre_focus = getGenreById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thể loại - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Thể loại Phim</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addGenreModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Thêm thể loại</span>
                        </button>
                    <?php endif; ?>
                    <div class="user-menu">
                        <img src="../../assets/images/default-avatar.png" alt="Admin">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thao tác thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <?php if ($action == 'edit' && $genre_focus): ?>
                    <div class="dashboard-card" style="max-width: 500px;">
                        <div class="card-header">
                            <h3>Chỉnh sửa thể loại</h3>
                            <a href="genres.php" class="btn-action">Quay lại</a>
                        </div>
                        <form action="../../Handle/genres_process.php" method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="genre_id" value="<?php echo $genre_focus['GenreID']; ?>">
                            
                            <div class="form-group">
                                <label>Tên thể loại</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($genre_focus['Name']); ?>" required class="custom-input">
                            </div>

                            <div class="modal-footer">
                                <a href="genres.php" class="btn-action">Hủy</a>
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
                                        <th style="width: 100px;">ID</th>
                                        <th>Tên thể loại</th>
                                        <th style="text-align: right;">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($genres)): ?>
                                        <tr><td colspan="3" class="text-center">Chưa có thể loại nào.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($genres as $g): ?>
                                        <tr>
                                            <td>#<?php echo $g['GenreID']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($g['Name']); ?></strong></td>
                                            <td style="text-align: right;">
                                                <a href="genres.php?action=edit&id=<?php echo $g['GenreID']; ?>" class="btn-action">Sửa</a>
                                                
                                                <form action="../../Handle/genres_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Xóa thể loại này?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="genre_id" value="<?php echo $g['GenreID']; ?>">
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

    <div id="addGenreModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Thêm thể loại mới</h2>
                <button class="btn-close" onclick="closeModal('addGenreModal')">&times;</button>
            </div>
            <form action="../../Handle/genres_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên thể loại</label>
                        <input type="text" name="name" placeholder="VD: Hành động, Kinh dị..." required class="custom-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addGenreModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>