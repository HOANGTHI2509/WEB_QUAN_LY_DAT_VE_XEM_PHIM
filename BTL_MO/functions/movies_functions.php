<?php
// BTL_MO/functions/movies_functions.php
// [File này dựa trên file bạn đã cung cấp]

require_once 'db_connect.php';

/**
 * Lấy tất cả danh sách phim từ CSDL
 * @return array Danh sách phim
 */
function getAllMovies() {
    $conn = getDbConnection(); 
    $movies = [];
    
    $sql = "SELECT MovieID, Title, Description, Duration, ReleaseDate, PosterURL, TrailerURL, Status, Director, Rating 
            FROM Movies 
            ORDER BY ReleaseDate DESC";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Đảm bảo ngày có định dạng Y-m-d
            if (isset($row['ReleaseDate'])) {
                $row['ReleaseDate'] = date('Y-m-d', strtotime($row['ReleaseDate']));
            }
            $movies[] = $row;
        }
    }
    
    $conn->close();
    return $movies;
}

/**
 * Lấy thông tin chi tiết của 1 phim
 * @param int $movie_id ID của phim
 * @return array|null Thông tin phim hoặc null nếu không tìm thấy
 */
function getMovieById($movie_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM Movies WHERE MovieID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $movie = null;
    if ($result->num_rows === 1) {
        $movie = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $movie;
}


/**
 * Thêm phim mới (dùng dữ liệu từ $_POST)
 * @param array $data Dữ liệu phim từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function addMovie($data) {
    $conn = getDbConnection();
    
    // Lấy dữ liệu từ mảng $data (chính là $_POST)
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $duration = $data['duration'] ?? 0;
    $director = $data['director'] ?? null;
    $posterUrl = $data['posterUrl'] ?? null;
    $trailerUrl = $data['trailerUrl'] ?? null;
    $status = $data['status'] ?? 'Sắp chiếu';
    $releaseDate = $data['releaseDate'] ?? null;
    $rating = 0.0; // Mặc định

    $sql = "INSERT INTO Movies (Title, Description, Duration, PosterURL, TrailerURL, Status, ReleaseDate, Director, Rating) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return "Lỗi chuẩn bị SQL: " . $conn->error;
    }

    $stmt->bind_param(
        "ssisssssd",
        $title, $description, $duration, $posterUrl, $trailerUrl, $status, $releaseDate, $director, $rating
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Thêm phim thất bại: " . $error;
    }
}

/**
 * Cập nhật phim (dùng dữ liệu từ $_POST)
 * @param array $data Dữ liệu phim từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function updateMovie($data) {
    $conn = getDbConnection();
    
    // Tên biến trong $data phải khớp với tên thẻ <input> trong form
    $movie_id = $data['MovieID'];
    $title = $data['Title'] ?? '';
    $description = $data['Description'] ?? '';
    $duration = $data['Duration'] ?? 0;
    $director = $data['Director'] ?? null;
    $posterUrl = $data['PosterURL'] ?? null;
    $trailerUrl = $data['TrailerURL'] ?? null;
    $status = $data['Status'] ?? 'Sắp chiếu';
    $releaseDate = $data['ReleaseDate'] ?? null;

    $sql = "UPDATE Movies SET 
                Title = ?, Description = ?, Duration = ?, Director = ?, 
                PosterURL = ?, TrailerURL = ?, Status = ?, ReleaseDate = ? 
            WHERE MovieID = ?";
            
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return "Lỗi chuẩn bị SQL: " . $conn->error;
    }
    
    $stmt->bind_param(
        "ssisssssi",
        $title, $description, $duration, $director,
        $posterUrl, $trailerUrl, $status, $releaseDate, $movie_id
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Cập nhật thất bại: " . $error;
    }
}

/**
 * Xóa phim
 * @param int $movie_id ID của phim
 * @return array Mảng chứa [success (bool), message (string)]
 */
function deleteMovie($movie_id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM Movies WHERE MovieID = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return ['success' => false, 'message' => "Lỗi chuẩn bị SQL: " . $conn->error];
    }
    
    $stmt->bind_param("i", $movie_id);

    try {
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = 'Xóa phim thành công!';
                $success = true;
            } else {
                $message = 'Không tìm thấy phim để xóa.';
                $success = false;
            }
        } else {
            $message = 'Xóa phim thất bại: ' . $stmt->error;
            $success = false;
        }
    } catch (mysqli_sql_exception $e) {
        // Bắt lỗi khóa ngoại (nếu phim đã có suất chiếu)
        if ($e->getCode() == 1451) { 
            $message = 'Không thể xóa phim này vì đã có suất chiếu. Bạn nên đổi trạng thái phim thành "Ngừng chiếu".';
            $success = false;
        } else {
            $message = 'Lỗi máy chủ: ' . $e->getMessage();
            $success = false;
        }
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
    
    return ['success' => $success, 'message' => $message];
}
?>