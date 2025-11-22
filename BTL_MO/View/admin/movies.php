<?php
// BTL_MO/View/admin/movies.php
include_once '../../functions/admin_gate.php';
include_once '../../functions/movies_functions.php';

// 1. LẤY DỮ LIỆU HỖ TRỢ (THỂ LOẠI & DIỄN VIÊN) - MỚI
$all_genres = getAllGenres();
$all_actors = getAllActors();

// 2. LẤY THAM SỐ TỪ URL
$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'MovieID';
$order = $_GET['order'] ?? 'DESC';
$filter_genre = $_GET['genre'] ?? '';

// 3. GỌI HÀM LẤY DANH SÁCH (Đã nâng cấp trong functions)
$data = getMoviesAdvanced($page, 10, $search, $status, $sort, $order, $filter_genre);
$movies_list = $data['data'];
$total_pages = $data['total_pages'];

// 4. LOGIC EDIT/DETAIL
$action = $_GET['action'] ?? 'list';
$movie_focus = null;

if (($action == 'edit' || $action == 'detail') && isset($_GET['id'])) {
    $movie_focus = getMovieById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phim - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* CSS PHÂN TRANG & FILTER */
        .pagination { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .page-link { padding: 8px 12px; border: 1px solid var(--border-color); text-decoration: none; color: var(--text-primary); border-radius: 4px; }
        .page-link.active { background: var(--primary-color); border-color: var(--primary-color); }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        
        /* CSS CHO THỂ LOẠI (CHECKBOX) */
        .genre-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; background: var(--bg-tertiary); padding: 15px; border-radius: 8px; max-height: 150px; overflow-y: auto; border: 1px solid var(--border-color); }
        .checkbox-item { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; color: var(--text-secondary); }
        .checkbox-item input { width: auto; } 
        
        /* CSS CHO DIỄN VIÊN (SELECT) */
        .actor-select { width: 100%; padding: 10px; background: var(--bg-tertiary); color: white; border: 1px solid var(--border-color); border-radius: 4px; height: 120px; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1>Quản lý phim</h1>
                <?php if ($action == 'list'): ?>
                    <button class="btn-add" onclick="openModal('addMovieModal')">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Thêm phim mới</span>
                    </button>
                <?php endif; ?>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <?php if ($action == 'detail' && $movie_focus): ?>
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Chi tiết phim: <?php echo htmlspecialchars($movie_focus['Title']); ?></h3>
                        <a href="movies.php" class="btn-action">Đóng</a>
                    </div>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                        <div style="width: 300px; flex-shrink: 0;">
                            <img src="../../<?php echo htmlspecialchars($movie_focus['PosterURL']); ?>" 
                                 alt="Poster" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                        </div>
                        <div style="flex: 1;">
                            <p style="margin-bottom: 10px;"><strong>ID Phim:</strong> #<?php echo $movie_focus['MovieID']; ?></p>
                            <p style="margin-bottom: 10px;"><strong>Đạo diễn:</strong> <?php echo htmlspecialchars($movie_focus['Director']); ?></p>
                            
                            <p style="margin-bottom: 10px;"><strong>Thể loại:</strong> 
                                <?php 
                                // Lưu ý: Hàm getMovieById hiện tại lấy ID thể loại. 
                                // Nếu muốn hiện tên ở đây, bạn có thể query thêm hoặc hiển thị đơn giản:
                                echo "<em>(Xem chi tiết ở danh sách)</em>"; 
                                ?>
                            </p>

                            <p style="margin-bottom: 10px;"><strong>Thời lượng:</strong> <?php echo $movie_focus['Duration']; ?> phút</p>
                            <p style="margin-bottom: 10px;"><strong>Ngày chiếu:</strong> <?php echo date('d/m/Y', strtotime($movie_focus['ReleaseDate'])); ?></p>
                            <p style="margin-bottom: 10px;"><strong>Trạng thái:</strong> <span class="badge badge-success"><?php echo $movie_focus['Status']; ?></span></p>
                            
                            <hr style="border-color: var(--border-color); margin: 15px 0;">
                            
                            <p><strong>Mô tả nội dung:</strong></p>
                            <p style="color: var(--text-secondary); line-height: 1.6; margin-top: 5px;">
                                <?php echo nl2br(htmlspecialchars($movie_focus['Description'])); ?>
                            </p>
                            
                            <div style="margin-top: 20px;">
                                <a href="<?php echo $movie_focus['TrailerURL']; ?>" target="_blank" class="btn-primary" style="display: inline-block; text-decoration: none;">
                                    Xem Trailer
                                </a>
                                <a href="movies.php?action=edit&id=<?php echo $movie_focus['MovieID']; ?>" class="btn-action">
                                    Chỉnh sửa phim này
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($action == 'edit' && $movie_focus): ?>
                    <div class="dashboard-card">
                        <h3>Chỉnh sửa phim</h3>
                        <form action="../../Handle/movies_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="MovieID" value="<?php echo $movie_focus['MovieID']; ?>">
                            <input type="hidden" name="PosterURL" value="<?php echo $movie_focus['PosterURL']; ?>">

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <div class="form-group">
                                        <label>Tên phim</label>
                                        <input type="text" name="Title" value="<?php echo htmlspecialchars($movie_focus['Title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Đạo diễn</label>
                                        <input type="text" name="Director" value="<?php echo htmlspecialchars($movie_focus['Director']); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Thể loại (Chọn nhiều)</label>
                                        <div class="genre-grid">
                                            <?php 
                                            $selected_genres = $movie_focus['GenreIDs'] ?? [];
                                            foreach ($all_genres as $g): ?>
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="genres[]" value="<?php echo $g['GenreID']; ?>" 
                                                        <?php echo in_array($g['GenreID'], $selected_genres) ? 'checked' : ''; ?>>
                                                    <?php echo htmlspecialchars($g['Name']); ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="form-group">
                                        <label>Chọn Diễn viên (Có sẵn)</label>
                                        <select name="actors[]" multiple class="actor-select">
                                            <?php 
                                            $selected_actors = $movie_focus['ActorIDs'] ?? [];
                                            foreach ($all_actors as $a): ?>
                                                <option value="<?php echo $a['ActorID']; ?>" 
                                                    <?php echo in_array($a['ActorID'], $selected_actors) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($a['Name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Hoặc nhập Diễn viên mới (cách nhau dấu phẩy)</label>
                                        <input type="text" name="new_actors" placeholder="VD: Trấn Thành, Trường Giang">
                                    </div>

                                    <div class="form-group">
                                        <label>Poster (Tải ảnh lên)</label>
                                        <div style="display: flex; gap: 15px; align-items: center;">
                                            <img id="previewEdit" src="../../<?php echo $movie_focus['PosterURL']; ?>" width="50" height="75" style="object-fit: cover;">
                                            <input type="file" name="poster_file" accept="image/*" onchange="previewFile(this, 'previewEdit')">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group"><label>Link Trailer</label><input type="text" name="TrailerURL" value="<?php echo htmlspecialchars($movie_focus['TrailerURL']); ?>"></div>
                            <div class="form-group"><label>Mô tả</label><textarea name="Description" rows="3"><?php echo htmlspecialchars($movie_focus['Description']); ?></textarea></div>
                            <div style="display:flex; gap:20px;">
                                <div class="form-group" style="flex:1"><label>Thời lượng</label><input type="number" name="Duration" value="<?php echo $movie_focus['Duration']; ?>"></div>
                                <div class="form-group" style="flex:1">
                                    <label>Trạng thái</label>
                                    <select name="Status">
                                        <option value="Đang chiếu" <?php echo ($movie_focus['Status']=='Đang chiếu')?'selected':'';?>>Đang chiếu</option>
                                        <option value="Sắp chiếu" <?php echo ($movie_focus['Status']=='Sắp chiếu')?'selected':'';?>>Sắp chiếu</option>
                                        <option value="Ngừng chiếu" <?php echo ($movie_focus['Status']=='Ngừng chiếu')?'selected':'';?>>Ngừng chiếu</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex:1"><label>Ngày chiếu</label><input type="date" name="ReleaseDate" value="<?php echo $movie_focus['ReleaseDate']; ?>"></div>
                            </div>

                            <div class="modal-footer">
                                <a href="movies.php" class="btn-action">Hủy</a>
                                <button type="submit" class="btn-primary">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <div class="dashboard-card">
                        <form method="GET" action="movies.php" class="filter-bar">
                            <input type="text" name="search" placeholder="Tìm tên phim..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                            
                            <select name="genre" style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                                <option value="">Tất cả thể loại</option>
                                <?php foreach ($all_genres as $g): ?>
                                    <option value="<?php echo $g['GenreID']; ?>" <?php echo ($filter_genre == $g['GenreID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($g['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select name="status" style="padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Đang chiếu" <?php echo $status == 'Đang chiếu' ? 'selected' : ''; ?>>Đang chiếu</option>
                                <option value="Sắp chiếu" <?php echo $status == 'Sắp chiếu' ? 'selected' : ''; ?>>Sắp chiếu</option>
                                <option value="Ngừng chiếu" <?php echo $status == 'Ngừng chiếu' ? 'selected' : ''; ?>>Ngừng chiếu</option>
                            </select>
                            
                            <button type="submit" class="btn-primary">Lọc</button>
                            <a href="movies.php" class="btn-action">Reset</a>
                        </form>

                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Poster</th>
                                        <th>Tên phim</th>
                                        <th>Thể loại</th> <th>Thời lượng</th>
                                        <th>Ngày chiếu</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movies_list as $movie): ?>
                                    <tr>
                                        <td>#<?php echo $movie['MovieID']; ?></td>
                                        <td>
                                            <a href="movies.php?action=detail&id=<?php echo $movie['MovieID']; ?>">
                                                <img src="../../<?php echo $movie['PosterURL']; ?>" 
                                                     style="width: 40px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #444;">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="movies.php?action=detail&id=<?php echo $movie['MovieID']; ?>" style="color: white; font-weight: bold; text-decoration: none;">
                                                <?php echo htmlspecialchars($movie['Title']); ?>
                                            </a>
                                        </td>
                                        
                                        <td style="max-width: 150px; color: #aaa; font-size: 13px;">
                                            <?php echo htmlspecialchars($movie['GenreNames'] ?? '---'); ?>
                                        </td>

                                        <td><?php echo $movie['Duration']; ?>p</td>
                                        <td><?php echo date('d/m/Y', strtotime($movie['ReleaseDate'])); ?></td>
                                        <td><span class="badge badge-success"><?php echo $movie['Status']; ?></span></td>
                                        <td>
                                            <a href="movies.php?action=edit&id=<?php echo $movie['MovieID']; ?>" class="btn-action">Sửa</a>
                                            <form action="../../Handle/movies_process.php" method="POST" style="display:inline;" onsubmit="return confirm('Xóa phim này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="movie_id" value="<?php echo $movie['MovieID']; ?>">
                                                <button class="btn-action danger">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="movies.php?page=<?php echo $i; ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div id="addMovieModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header"><h2>Thêm phim mới</h2><button class="btn-close" onclick="closeModal('addMovieModal')">&times;</button></div>
            <form action="../../Handle/movies_process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <div class="form-group"><label>Tên phim</label><input type="text" name="title" required></div>
                            <div class="form-group"><label>Đạo diễn</label><input type="text" name="director"></div>
                            
                            <div class="form-group">
                                <label>Thể loại (Chọn nhiều)</label>
                                <div class="genre-grid">
                                    <?php foreach ($all_genres as $g): ?>
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="genres[]" value="<?php echo $g['GenreID']; ?>">
                                            <?php echo htmlspecialchars($g['Name']); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label>Chọn Diễn viên (Có sẵn - giữ Ctrl để chọn nhiều)</label>
                                <select name="actors[]" multiple class="actor-select">
                                    <?php foreach ($all_actors as $a): ?>
                                        <option value="<?php echo $a['ActorID']; ?>"><?php echo htmlspecialchars($a['Name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Hoặc nhập tên Diễn viên mới (dấu phẩy)</label>
                                <input type="text" name="new_actors" placeholder="VD: Trấn Thành, Trường Giang">
                            </div>

                            <div class="form-group">
                                <label>Poster (Tải ảnh lên)</label>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <img id="previewAdd" src="https://via.placeholder.com/50" width="50" height="75" style="object-fit:cover">
                                    <input type="file" name="poster_file" accept="image/*" onchange="previewFile(this, 'previewAdd')">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group"><label>Link Trailer (Youtube)</label><input type="text" name="trailerUrl"></div>
                    <div class="form-group"><label>Mô tả</label><textarea name="description" rows="2"></textarea></div>
                    <div style="display:flex; gap:10px;">
                        <div class="form-group" style="flex:1"><label>Thời lượng (phút)</label><input type="number" name="duration" required></div>
                        <div class="form-group" style="flex:1"><label>Trạng thái</label><select name="status"><option>Sắp chiếu</option><option>Đang chiếu</option></select></div>
                        <div class="form-group" style="flex:1"><label>Ngày chiếu</label><input type="date" name="releaseDate"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn-action" onclick="closeModal('addMovieModal')">Hủy</button><button type="submit" class="btn-primary">Lưu</button></div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/all_effects.js"></script>
    <script>
    function previewFile(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        reader.addEventListener("load", function () { preview.src = reader.result; }, false);
        if (file) reader.readAsDataURL(file);
    }
    </script>
</body>
</html>