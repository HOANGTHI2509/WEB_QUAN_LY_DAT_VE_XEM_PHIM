<?php
// BTL_MO/View/admin/seats.php
include_once '../../functions/admin_gate.php';
include_once '../../functions/screens_functions.php';
include_once '../../functions/seats_functions.php';

// 1. LẤY ID PHÒNG CHIẾU
$screen_id = $_GET['screen_id'] ?? 0;
if (!$screen_id) {
    header("location: screens.php");
    exit;
}

// 2. LẤY DỮ LIỆU
$screen = getScreenById($screen_id); // Đảm bảo hàm này đã JOIN bảng Theaters để lấy tên rạp
$seats = getSeatsByScreen($screen_id);
$seat_types = getSeatTypes();

// 3. GOM NHÓM GHẾ
$seat_map = [];
foreach ($seats as $s) {
    $seat_map[$s['RowName']][] = $s;
}

// Xử lý hiển thị tên rạp an toàn
$theaterName = $screen['TheaterName'] ?? 'Rạp chiếu phim';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sơ đồ ghế - <?php echo htmlspecialchars($screen['Name']); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .editor-area {
            background: #222;
            padding: 40px 20px;
            border-radius: 12px;
            text-align: center;
            overflow-x: auto;
            min-height: 400px;
            scrollbar-width: thin;
            scrollbar-color: #444 #222;
        }
        .screen-line {
            height: 8px; background: #444; width: 50%; margin: 0 auto 40px;
            box-shadow: 0 10px 20px rgba(255,255,255,0.05); border-radius: 50%;
        }
        .seat-grid { display: inline-block; text-align: left; }
        .seat-row { display: flex; gap: 6px; margin-bottom: 6px; align-items: center; }
        .row-label { width: 25px; font-weight: bold; color: #666; text-align: center; font-size: 12px; }
        
        .seat-item {
            width: 32px; height: 32px;
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; color: #fff; font-weight: 600;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.2s;
            text-decoration: none;
            position: relative;
        }
        .seat-item:hover { transform: scale(1.15); z-index: 10; border-color: #fff; }
        
        .btn-x {
            position: absolute; top: -6px; right: -6px;
            width: 14px; height: 14px;
            background: #ff4444; color: white;
            border: none; border-radius: 50%;
            font-size: 9px; display: none;
            align-items: center; justify-content: center;
            cursor: pointer; z-index: 11;
        }
        .seat-item:hover .btn-x { display: flex; }

        /* MÀU GHẾ (Cần khớp ID trong DB của bạn) */
        /* Nếu DB của bạn: 2=Thường, 3=VIP, sửa số ở đây */
        .type-1, .type-2 { background: #555; } /* Thường */
        .type-2, .type-3 { background: #e50914; border-color: #b20710; } /* VIP */

        .legend { display: flex; gap: 20px; justify-content: center; margin-bottom: 20px; background: var(--bg-tertiary); padding: 15px; border-radius: 8px;}
        .legend-item { display: flex; gap: 8px; align-items: center; font-size: 13px; color: #ccc; }
        .dot { width: 16px; height: 16px; border-radius: 4px; }
        
        .stats-box {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(70, 211, 105, 0.1);
            border: 1px solid rgba(70, 211, 105, 0.3);
            border-radius: 8px;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'partials/sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="screens.php" class="btn-action">← Quay lại</a>
                    <h1>Sơ đồ ghế: <?php echo htmlspecialchars($screen['Name']); ?></h1>
                </div>
                <div style="color: #888;"><?php echo htmlspecialchars($theaterName); ?></div>
                
                <?php if (!empty($seats)): ?>
                <form action="../../Handle/seats_process.php" method="POST" onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ xóa sạch sơ đồ hiện tại để làm lại!');">
                    <input type="hidden" name="action" value="reset">
                    <input type="hidden" name="screen_id" value="<?php echo $screen_id; ?>">
                    <button class="btn-action danger">Xóa sơ đồ & Làm lại</button>
                </form>
                <?php endif; ?>
            </header>

            <div class="dashboard-content">
                <?php if (isset($_GET['success'])): ?><div class="alert alert-success">Thành công!</div><?php endif; ?>
                <?php if (isset($_GET['error'])): ?><div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <?php if (empty($seats)): ?>
                <div class="dashboard-card" style="max-width: 600px; margin: 40px auto; text-align: center;">
                    <h3>Khởi tạo sơ đồ ghế</h3>
                    <div class="stats-box">
                        Phòng này có sức chứa: <strong style="color: #46d369; font-size: 18px;"><?php echo $screen['Capacity']; ?></strong> ghế.
                    </div>
                    <p style="color: #aaa; margin-bottom: 30px; line-height: 1.5;">
                        Hệ thống sẽ tự động tính toán số hàng/cột để khớp với sức chứa.<br>
                        (Mặc định 3 hàng đầu là Ghế Thường, còn lại là VIP)
                    </p>
                    
                    <form action="../../Handle/seats_process.php" method="POST">
                        <input type="hidden" name="action" value="generate">
                        <input type="hidden" name="screen_id" value="<?php echo $screen_id; ?>">
                        <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
                            Tạo sơ đồ ngay
                        </button>
                    </form>
                </div>

                <?php else: ?>
                    <div class="legend">
                        <?php foreach ($seat_types as $t): ?>
                            <div class="legend-item">
                                <div class="dot type-<?php echo $t['SeatTypeID']; ?>"></div> 
                                <?php echo $t['Name']; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="legend-item" style="margin-left: 15px; border-left: 1px solid #444; padding-left: 15px;">
                            👉 Click ghế để đổi loại | ❌ Click dấu X để xóa
                        </div>
                    </div>

                    <div class="editor-area">
                        <div class="screen-line"></div>
                        <div style="margin-bottom: 30px; font-size: 12px; color: #666; letter-spacing: 2px;">MÀN HÌNH</div>

                        <div class="seat-grid">
                            <?php foreach ($seat_map as $rowName => $rowSeats): ?>
                                <div class="seat-row">
                                    <div class="row-label"><?php echo $rowName; ?></div>
                                    <?php foreach ($rowSeats as $s): ?>
                                        <div style="position: relative;">
                                            <a href="../../Handle/seats_process.php?action=toggle&seat_id=<?php echo $s['SeatID']; ?>" 
                                               class="seat-item type-<?php echo $s['SeatTypeID']; ?>"
                                               title="Loại: <?php echo $s['SeatTypeName']; ?>">
                                                <?php echo $s['SeatNumber']; ?>
                                                
                                                <form action="../../Handle/seats_process.php" method="POST" style="display:contents;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="seat_id" value="<?php echo $s['SeatID']; ?>">
                                                    <input type="hidden" name="screen_id" value="<?php echo $screen_id; ?>">
                                                    <button type="submit" class="btn-x" title="Xóa" onclick="event.stopPropagation(); return confirm('Xóa ghế này?');">×</button>
                                                </form>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="../../assets/js/all_effects.js"></script>
</body>
</html>