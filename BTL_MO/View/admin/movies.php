<?php
// BTL_MO/View/admin/movies.php

include_once '../../functions/admin_gate.php';
include_once '../../functions/movies_functions.php';

$action = $_GET['action'] ?? 'list'; 
$movie_to_edit = null; 

if ($action == 'edit' && isset($_GET['id'])) {
    $movie_to_edit = getMovieById((int)$_GET['id']);
}

if ($action == 'add') {
    $movie_to_edit = [
        'MovieID' => '', 'Title' => '', 'Description' => '', 'Duration' => '', 
        'Director' => '', 'PosterURL' => '', 'TrailerURL' => '', 
        'Status' => 'Sắp chiếu', 'ReleaseDate' => ''
    ];
}

$movies_list = [];
if ($action == 'list') {
    $movies_list = getAllMovies();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phim - CinemaHub Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý phim</h1>
                <div class="header-actions">
                    <?php if ($action == 'list'): ?>
                        <button class="btn-add" onclick="openModal('addMovieModal')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Thêm phim mới</span>
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
                        if ($_GET['success'] == 'add') echo "Thêm phim mới thành công!";
                        if ($_GET['success'] == 'update') echo "Cập nhật phim thành công!";
                        if ($_GET['success'] == 'delete') echo "Xóa phim thành công!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($action == 'edit' || $action == 'add'): ?>
                
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo ($action == 'edit') ? 'Chỉnh sửa phim' : 'Thêm phim mới'; ?></h3>
                    </div>
                    
                    <form action="../../Handle/movies_process.php" method="POST">
                        
                        <?php if ($action == 'edit'): ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="MovieID" value="<?php echo $movie_to_edit['MovieID']; ?>">
                        <?php else: ?>
                            <?php endif; ?>

                        <div class="form-group">
                            <label>Tên phim</label>
                            <input type="text" name="Title" value="<?php echo htmlspecialchars($movie_to_edit['Title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea name="Description" rows="4"><?php echo htmlspecialchars($movie_to_edit['Description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Thời lượng (phút)</label>
                            <input type="number" name="Duration" value="<?php echo htmlspecialchars($movie_to_edit['Duration']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Đạo diễn</label>
                            <input type="text" name="Director" value="<?php echo htmlspecialchars($movie_to_edit['Director']); ?>">
                        </div>
                        <div class="form-group">
                            <label>URL Poster</label>
                            <input type="url" name="PosterURL" value="<?php echo htmlspecialchars($movie_to_edit['PosterURL']); ?>">
                        </div>
                        <div class="form-group">
                            <label>URL Trailer</label>
                            <input type="url" name="TrailerURL" value="<?php echo htmlspecialchars($movie_to_edit['TrailerURL']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="Status" required>
                                <option value="Đang chiếu" <?php echo ($movie_to_edit['Status'] == 'Đang chiếu') ? 'selected' : ''; ?>>Đang chiếu</option>
                                <option value="Sắp chiếu" <?php echo ($movie_to_edit['Status'] == 'Sắp chiếu') ? 'selected' : ''; ?>>Sắp chiếu</option>
                                <option value="Ngừng chiếu" <?php echo ($movie_to_edit['Status'] == 'Ngừng chiếu') ? 'selected' : ''; ?>>Ngừng chiếu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ngày phát hành</label>
                            <input type="date" name="ReleaseDate" value="<?php echo htmlspecialchars($movie_to_edit['ReleaseDate']); ?>">
                        </div>
                        <div class="modal-footer">
                            <a href="movies.php" class="btn-action">Hủy</a>
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
                                    <th>Poster</th>
                                    <th>Tên phim</th>
                                    <th>Thời lượng</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody id="moviesTableBody">
                                <?php if (empty($movies_list)): ?>
                                    <tr><td colspan="6" class="text-center">Chưa có phim nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($movies_list as $movie): ?>
                                    <tr>
                                        <td><strong>#<?php echo $movie['MovieID']; ?></strong></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($movie['PosterURL'] ?? 'https://via.placeholder.com/50x75'); ?>"
                                                 alt="<?php echo htmlspecialchars($movie['Title']); ?>"
                                                 style="width: 50px; height: 75px; object-fit: cover; border-radius: 4px;">
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($movie['Title']); ?></strong></td>
                                        <td><?php echo $movie['Duration']; ?> phút</td>
                                        <td>
                                            <span class="badge <?php 
                                                if($movie['Status'] == 'Đang chiếu') echo 'badge-success';
                                                else if($movie['Status'] == 'Sắp chiếu') echo 'badge-warning';
                                                else echo 'badge-danger';
                                            ?>"><?php echo htmlspecialchars($movie['Status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="movies.php?action=edit&id=<?php echo $movie['MovieID']; ?>" class="btn-action">
                                                Sửa
                                            </a>
                                            <form action="../../Handle/movies_process.php" method="POST" style="display: inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa phim này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="movie_id" value="<?php echo $movie['MovieID']; ?>">
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

    <div id="addMovieModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm phim mới</h2>
                <button class="btn-close" onclick="closeModal('addMovieModal')">&times;</button>
            </div>
            <form id="addMovieForm" action="../../Handle/movies_process.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên phim</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Thời lượng (phút)</label>
                        <input type="number" name="duration" required>
                    </div>
                    <div class="form-group">
                        <label>Đạo diễn</label>
                        <input type="text" name="director">
                    </div>
                    <div class="form-group">
                        <label>URL Poster</label>
                        <input type="url" name="posterUrl" placeholder="https://example.com/image.png">
                    </div>
                    <div class="form-group">
                        <label>URL Trailer</label>
                        <input type="url" name="trailerUrl" placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" required>
                            <option value="Sắp chiếu" selected>Sắp chiếu</option>
                            <option value="Đang chiếu">Đang chiếu</option>
                            <option value="Ngừng chiếu">Ngừng chiếu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ngày phát hành</label>
                        <input type="date" name="releaseDate">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-action" onclick="closeModal('addMovieModal')">Hủy</button>
                    <button type="submit" class="btn-primary">Thêm phim</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>