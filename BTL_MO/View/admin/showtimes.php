<?php
// BTL_MO/View/admin/showtimes.php

session_start();
// 1. Gọi file functions
require_once '../../functions/showtimes_functions.php';
require_once '../../functions/movies_functions.php';
require_once '../../functions/theaters_functions.php';
// 2. Kiểm tra quyền Admin
require_once '../../functions/admin_gate.php';

// --- LẤY DỮ LIỆU CHO DROPDOWN ---
$all_movies = getAllMovies();
$all_theaters = getAllTheaters();

// --- XỬ LÝ LỌC (THUẦN PHP - LẤY TỪ URL) ---
// Nếu không chọn ngày, mặc định lấy ngày hiện tại
$filter_date = $_GET['filter_date'] ?? date('Y-m-d'); 
$filter_movie = $_GET['filter_movie'] ?? '';
$filter_theater = $_GET['filter_theater'] ?? '';

$filters = [
    'date' => $filter_date,
    'movie_id' => $filter_movie,
    'theater_id' => $filter_theater
];

// Lấy danh sách suất chiếu theo bộ lọc
$showtimes_list = getFilteredShowtimes($filters);

// --- XỬ LÝ LOGIC CHO MODAL THÊM (PHP RENDER SẴN DATA) ---
// Để không dùng API, ta lấy tất cả phòng chiếu và gom nhóm theo Rạp ngay từ đầu
// Sau đó dùng JS đơn giản để lọc phía client (không cần gọi server)
$conn = getDbConnection();
$all_screens_raw = $conn->query("SELECT ScreenID, TheaterID, Name, Capacity FROM Screens ORDER BY Name")->fetch_all(MYSQLI_ASSOC);
$screens_by_theater = [];
foreach ($all_screens_raw as $scr) {
    $screens_by_theater[$scr['TheaterID']][] = $scr;
}
// Chuyển mảng PHP sang JSON để dùng trong JS (Cách tối ưu cho thuần PHP không API)
$screens_json = json_encode($screens_by_theater);

// Hàm helper màu sắc trạng thái ghế
function getStatusColor($seats_available, $total_seats) {
    if ($total_seats == 0) return '#777';
    $ratio = $seats_available / $total_seats;
    if ($ratio == 0) return '#e50914'; // Hết vé (Đỏ)
    if ($ratio < 0.2) return '#ffa500'; // Sắp hết (Cam)
    return '#46d369'; // Còn nhiều (Xanh)
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Suất chiếu - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <style>
        /* --- CSS CHO BỘ LỌC ĐẸP (DARK THEME) --- */
        .filter-container {
            background: var(--bg-secondary);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto; /* 3 cột nhập liệu + 1 cột nút */
            gap: 20px;
            align-items: end; /* Căn đáy để nút bằng với input */
        }

        .form-group-filter {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group-filter label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Style Input/Select */
        .dark-input {
            width: 100%;
            padding: 10px 15px;
            background-color: #222; /* Nền rất tối */
            border: 1px solid #444;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
            height: 42px; /* Chiều cao cố định */
            transition: all 0.2s;
        }

        .dark-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.15);
        }

        /* Nút Lọc */
        .btn-filter-submit {
            height: 42px;
            padding: 0 25px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-filter-submit:hover {
            background-color: var(--primary-dark);
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr; /* 1 cột dọc */
            }
            .btn-filter-submit {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý Suất chiếu</h1>
                <div class="header-actions">
                    <button class="btn-add" onclick="openModal('addShowtimeModal')">
                        <span>+ Thêm suất chiếu</span>
                    </button>
                    <div class="user-menu">
                        <img src="../../assets/images/default-avatar.png" alt="Admin">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thao tác thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <div class="filter-container">
                    <form action="" method="GET" class="filter-form">
                        
                        <div class="form-group-filter">
                            <label>Ngày chiếu</label>
                            <input type="date" name="filter_date" class="dark-input" 
                                   value="<?php echo $filter_date; ?>">
                        </div>

                        <div class="form-group-filter">
                            <label>Phim</label>
                            <select name="filter_movie" class="dark-input">
                                <option value="">-- Tất cả phim --</option>
                                <?php foreach ($all_movies as $m): ?>
                                    <option value="<?php echo $m['MovieID']; ?>" 
                                        <?php echo ($filter_movie == $m['MovieID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['Title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group-filter">
                            <label>Rạp chiếu</label>
                            <select name="filter_theater" class="dark-input">
                                <option value="">-- Tất cả rạp --</option>
                                <?php foreach ($all_theaters as $t): ?>
                                    <option value="<?php echo $t['TheaterID']; ?>" 
                                        <?php echo ($filter_theater == $t['TheaterID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn-filter-submit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                            Lọc
                        </button>

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
                                    <tr>
                                        <td colspan="7" class="text-center" style="padding: 40px; color: #777;">
                                            Không tìm thấy suất chiếu nào theo bộ lọc này.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($showtimes_list as $st): 
                                        $color = getStatusColor($st['SeatsAvailable'], $st['Capacity']);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($st['MovieTitle']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($st['TheaterName']); ?></td>
                                        <td><?php echo htmlspecialchars($st['ScreenName']); ?></td>
                                        <td>
                                            <div style="font-weight: bold;"><?php echo date('d/m/Y', strtotime($st['StartTime'])); ?></div>
                                            <div style="font-size: 13px; color: var(--primary-color);"><?php echo date('H:i', strtotime($st['StartTime'])); ?></div>
                                        </td>
                                        <td><?php echo number_format($st['Price'], 0, ',', '.'); ?> ₫</td>
                                        <td>
                                            <span style="color: <?php echo $color; ?>; font-weight: bold;">
                                                <?php echo $st['SeatsAvailable']; ?> / <?php echo $st['Capacity']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="../../Handle/showtimes_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Xóa suất chiếu này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="showtime_id" value="<?php echo $st['ShowtimeID']; ?>">
                                                <input type="hidden" name="filter_date" value="<?php echo $filter_date; ?>">
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
            </div>
        </main>
    </div>

    <div id="addShowtimeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header"><h2>Thêm suất chiếu mới</h2><button class="btn-close" onclick="closeModal('addShowtimeModal')">&times;</button></div>
            <form action="../../Handle/showtimes_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn Rạp</label>
                        <select id="modalTheater" class="dark-input" required onchange="updateScreens()">
                            <option value="">-- Chọn rạp --</option>
                            <?php foreach ($all_theaters as $t): ?>
                                <option value="<?php echo $t['TheaterID']; ?>"><?php echo htmlspecialchars($t['Name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phòng chiếu</label>
                        <select name="screen_id" id="modalScreen" class="dark-input" required disabled>
                            <option value="">-- Vui lòng chọn rạp trước --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phim</label>
                        <select name="movie_id" class="dark-input" required>
                            <?php foreach ($all_movies as $m): ?>
                                <?php if($m['Status'] == 'Đang chiếu' || $m['Status'] == 'Sắp chiếu'): ?>
                                    <option value="<?php echo $m['MovieID']; ?>"><?php echo htmlspecialchars($m['Title']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Ngày chiếu</label>
                            <input type="date" name="show_date" class="dark-input" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div>
                            <label>Giờ chiếu</label>
                            <input type="time" name="show_time" class="dark-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Giá vé cơ bản (VNĐ)</label>
                        <input type="number" name="price" class="dark-input" value="90000" min="0" step="1000" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addShowtimeModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm suất chiếu</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
    
    <script>
        // PHP in dữ liệu ra biến JS
        const screensData = <?php echo $screens_json; ?>;

        function updateScreens() {
            const theaterId = document.getElementById('modalTheater').value;
            const screenSelect = document.getElementById('modalScreen');

            screenSelect.innerHTML = '<option value="">-- Chọn phòng --</option>';
            
            if (theaterId && screensData[theaterId]) {
                screenSelect.disabled = false;
                screensData[theaterId].forEach(screen => {
                    const opt = document.createElement('option');
                    opt.value = screen.ScreenID;
                    opt.textContent = `${screen.Name} (${screen.Capacity} ghế)`;
                    screenSelect.appendChild(opt);
                });
            } else {
                screenSelect.disabled = true;
                screenSelect.innerHTML = '<option value="">-- Rạp này chưa có phòng --</option>';
            }
        }
    </script>
</body>
</html>