<?php
// BTL_MO/View/admin/showtimes.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/showtimes_functions.php';
include_once '../../functions/movies_functions.php';
include_once '../../functions/theaters_functions.php';

$action = $_GET['action'] ?? 'list'; 

$filter_date = $_GET['filter_date'] ?? date('Y-m-d');
$filter_movie = $_GET['filter_movie'] ?? null;
$filter_theater = $_GET['filter_theater'] ?? null;
$selected_theater = $_GET['selected_theater'] ?? null; 

$movies_list = getAllMovies();
$theaters_list = getAllTheaters();
$screens_list = []; 

$showtimes_list = [];
if ($action == 'list') {
    $filters = [
        'date' => $filter_date,
        'movie_id' => $filter_movie,
        'theater_id' => $filter_theater
    ];
    $showtimes_list = getFilteredShowtimes($filters);
}

if ($action == 'add' && !empty($selected_theater)) {
    $screens_list = getScreensByTheater((int)$selected_theater);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý suất chiếu - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý suất chiếu</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                    <a href="showtimes.php?action=add" class="btn-add">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Thêm suất chiếu</span>
                    </a>
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
                        if ($_GET['success'] == 'add') echo "Thêm suất chiếu thành công!";
                        if ($_GET['success'] == 'update') echo "Cập nhật suất chiếu thành công!";
                        if ($_GET['success'] == 'delete') echo "Xóa suất chiếu thành công!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Thêm suất chiếu mới</h3>
                    </div>
                    
                    <form method="GET" action="showtimes.php" id="loadScreensForm">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Bước 1: Chọn Rạp (*)</label>
                            <div style="display: flex; gap: 10px;">
                                <select name="selected_theater" required style="flex: 1;">
                                    <option value="">-- Chọn rạp --</option>
                                    <?php foreach ($theaters_list as $theater): ?>
                                        <option value="<?php echo $theater['TheaterID']; ?>"
                                            <?php echo ($selected_theater == $theater['TheaterID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($theater['Name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-action">Tải phòng</button>
                            </div>
                        </div>
                    </form>

                    <hr style="border-color: var(--border-color); margin: 20px 0;">

                    <form action="../../Handle/showtimes_process.php" method="POST">
                        <input type="hidden" name="action" value="add">

                        <div class="form-group">
                            <label>Bước 2: Chọn phòng chiếu (*)</label>
                            <select name="screen_id" required>
                                <?php if (empty($screens_list)): ?>
                                    <option value="">-- Vui lòng chọn rạp và nhấn "Tải phòng" ở trên --</option>
                                <?php else: ?>
                                    <option value="">-- Chọn phòng --</option>
                                    <?php foreach ($screens_list as $screen): ?>
                                        <option value="<?php echo $screen['ScreenID']; ?>">
                                            <?php echo htmlspecialchars($screen['Name']); ?> (<?php echo $screen['Capacity']; ?> ghế)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Bước 3: Chọn phim (*)</label>
                            <select name="movie_id" required>
                                <option value="">-- Chọn phim --</option>
                                <?php foreach ($movies_list as $movie): ?>
                                    <?php if($movie['Status'] == 'Đang chiếu'): ?>
                                    <option value="<?php echo $movie['MovieID']; ?>">
                                        <?php echo htmlspecialchars($movie['Title']); ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bước 4: Ngày chiếu (*)</label>
                            <input type="date" name="show_date" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Bước 5: Giờ chiếu (*)</label>
                            <input type="time" name="show_time" required>
                        </div>
                        <div class="form-group">
                            <label>Bước 6: Giá vé (VNĐ) (*)</label>
                            <input type="number" name="price" required step="1000" min="0">
                        </div>
                        
                        <div class="modal-footer">
                            <a href="showtimes.php" class="btn-action">Hủy</a>
                            <button type="submit" class="btn-primary" 
                                <?php if (empty($screens_list)) echo 'disabled'; ?>>
                                Lưu suất chiếu
                            </button>
                            <?php if (empty($screens_list)): ?>
                                <small style="color: var(--warning-color); margin-left: 10px;">Bạn phải "Tải phòng" trước khi lưu.</small>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                
                <div class="dashboard-card" style="margin-bottom: 20px;">
                    <form method="GET" action="showtimes.php" class="filter-bar">
                        <input type="hidden" name="action" value="list">
                        <div class="filter-group">
                            <label>Ngày chiếu</label>
                            <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>" class="filter-input">
                        </div>
                        <div class="filter-group">
                            <label>Phim</label>
                            <select name="filter_movie" class="filter-select">
                                <option value="">Tất cả phim</option>
                                <?php foreach ($movies_list as $movie): ?>
                                    <option value="<?php echo $movie['MovieID']; ?>" <?php echo ($filter_movie == $movie['MovieID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($movie['Title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Rạp</label>
                            <select name="filter_theater" class="filter-select">
                                <option value="">Tất cả rạp</option>
                                <?php foreach ($theaters_list as $theater): ?>
                                    <option value="<?php echo $theater['TheaterID']; ?>" <?php echo ($filter_theater == $theater['TheaterID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($theater['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Lọc</button>
                    </form>
                </div>

                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Phim</th>
                                    <th>Rạp</th>
                                    <th>Phòng</th>
                                    <th>Thời gian</th>
                                    <th>Giá vé</th>
                                    <th>Ghế trống</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($showtimes_list)): ?>
                                    <tr><td colspan="7" class="text-center">Không có suất chiếu nào cho bộ lọc này.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($showtimes_list as $st): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($st['MovieTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($st['TheaterName']); ?></td>
                                        <td><?php echo htmlspecialchars($st['ScreenName']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($st['StartTime'])); ?></td>
                                        <td><?php echo number_format($st['Price'], 0, ',', '.'); ?> ₫</td>
                                        <td><?php echo $st['SeatsAvailable']; ?> / <?php echo $st['Capacity']; ?></td>
                                        <td>
                                            <form action="../../Handle/showtimes_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa suất chiếu này? (Chỉ xóa được nếu chưa có vé bán)');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="showtime_id" value="<?php echo $st['ShowtimeID']; ?>">
                                                <input type="hidden" name="filter_date" value="<?php echo $filter_date; ?>">
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

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>