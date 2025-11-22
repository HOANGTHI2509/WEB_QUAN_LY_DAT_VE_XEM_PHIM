<?php
// BTL_MO/functions/movies_functions.php
require_once 'db_connect.php';

function getAllGenres() {
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM Genres ORDER BY Name");
    $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $data;
}

function getAllActors() {
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM Actors ORDER BY Name");
    $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $data;
}

function getGenresByMovie($movie_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT GenreID FROM movie_genres WHERE MovieID = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) $ids[] = $row['GenreID'];
    $stmt->close();
    $conn->close();
    return $ids;
}

function getActorsByMovie($movie_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT ActorID FROM movie_actors WHERE MovieID = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) $ids[] = $row['ActorID'];
    $stmt->close();
    $conn->close();
    return $ids;
}



function uploadMoviePoster($file) {
    if (!isset($file['name']) || $file['error'] != 0) return null;
    $target_dir = "../assets/uploads/movies/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_name = "movie_" . time() . "_" . rand(1000, 9999) . "." . $extension;
    if (move_uploaded_file($file["tmp_name"], $target_dir . $new_name)) {
        return "assets/uploads/movies/" . $new_name;
    }
    return null;
}

function getMoviesAdvanced($page = 1, $limit = 10, $search = '', $status = '', $sort = 'MovieID', $order = 'DESC', $genre_id = '') {
    $conn = getDbConnection();
    $offset = ($page - 1) * $limit;
    $movies = [];

    $sql = "SELECT SQL_CALC_FOUND_ROWS m.*, 
            GROUP_CONCAT(DISTINCT g.Name SEPARATOR ', ') as GenreNames
            FROM Movies m
            LEFT JOIN movie_genres mg ON m.MovieID = mg.MovieID
            LEFT JOIN Genres g ON mg.GenreID = g.GenreID
            WHERE 1=1";
    
    $types = "";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND m.Title LIKE ?";
        $types .= "s";
        $params[] = "%" . $search . "%";
    }
    if (!empty($status)) {
        $sql .= " AND m.Status = ?";
        $types .= "s";
        $params[] = $status;
    }
    if (!empty($genre_id)) {
        $sql .= " AND m.MovieID IN (SELECT MovieID FROM movie_genres WHERE GenreID = ?)";
        $types .= "i";
        $params[] = $genre_id;
    }

    $sql .= " GROUP BY m.MovieID"; 
    
    $allowed_sorts = ['MovieID', 'Title', 'Duration', 'ReleaseDate'];
    if (!in_array($sort, $allowed_sorts)) $sort = 'MovieID';
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
    $sql .= " ORDER BY m.$sort $order";

    $sql .= " LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $conn->prepare($sql);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) $movies = $result->fetch_all(MYSQLI_ASSOC);

    $result_total = $conn->query("SELECT FOUND_ROWS() as total");
    $total_rows = $result_total->fetch_assoc()['total'];
    $total_pages = ceil($total_rows / $limit);

    $stmt->close();
    $conn->close();

    return ['data' => $movies, 'total_pages' => $total_pages, 'total_rows' => $total_rows];
}

function getAllMovies() {
    $conn = getDbConnection(); 
    $sql = "SELECT * FROM Movies ORDER BY ReleaseDate DESC";
    $result = $conn->query($sql);
    $movies = ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $movies;
}

function getMovieById($movie_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM Movies WHERE MovieID = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($movie) {
        $movie['GenreIDs'] = getGenresByMovie($movie_id);
        $movie['ActorIDs'] = getActorsByMovie($movie_id);
    }
    return $movie;
}


function processNewActors($conn, $new_actors_string) {
    $new_ids = [];
    if (empty(trim($new_actors_string))) return $new_ids;

    // Tách chuỗi bằng dấu phẩy
    $names = explode(',', $new_actors_string);
    
    foreach ($names as $name) {
        $name = trim($name);
        if (empty($name)) continue;

        // Kiểm tra tồn tại
        $check = $conn->prepare("SELECT ActorID FROM Actors WHERE Name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $new_ids[] = $row['ActorID'];
        } else {
            // Thêm mới
            $insert = $conn->prepare("INSERT INTO Actors (Name) VALUES (?)");
            $insert->bind_param("s", $name);
            if ($insert->execute()) {
                $new_ids[] = $conn->insert_id;
            }
        }
    }
    return $new_ids;
}

/* ============================================== */
/* HÀM ADD & UPDATE (CẬP NHẬT LOGIC) */
/* ============================================== */

function addMovie($data) {
    $conn = getDbConnection();
    $conn->begin_transaction();

    try {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $duration = $data['duration'] ?? 0;
        $director = $data['director'] ?? null;
        $posterUrl = $data['poster_file_path'] ?? ($data['posterUrl'] ?? '');
        $trailerUrl = $data['trailerUrl'] ?? null;
        $status = $data['status'] ?? 'Sắp chiếu';
        $releaseDate = $data['releaseDate'] ?? null;
        $rating = 0.0;

        // 1. Insert Movies
        $sql = "INSERT INTO Movies (Title, Description, Duration, Director, PosterURL, TrailerURL, Status, ReleaseDate, Director, Rating) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        // Lưu ý: bind_param ssisssssd (9 tham số)
        // Nhưng SQL ở trên tôi viết thừa 1 dấu ? cho Director (đã có) -> Sửa lại SQL:
        $sql = "INSERT INTO Movies (Title, Description, Duration, PosterURL, TrailerURL, Status, ReleaseDate, Director, Rating) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssssd", $title, $description, $duration, $posterUrl, $trailerUrl, $status, $releaseDate, $director, $rating);
        $stmt->execute();
        $movie_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert Thể loại
        if (!empty($data['genres'])) {
            $stmt_g = $conn->prepare("INSERT INTO movie_genres (MovieID, GenreID) VALUES (?, ?)");
            foreach ($data['genres'] as $genre_id) {
                $stmt_g->bind_param("ii", $movie_id, $genre_id);
                $stmt_g->execute();
            }
            $stmt_g->close();
        }

        // 3. Xử lý Diễn viên (Cũ + Mới)
        $actor_ids = $data['actors'] ?? []; // Từ Select box
        if (!empty($data['new_actors'])) {
            $new_ids = processNewActors($conn, $data['new_actors']);
            $actor_ids = array_merge($actor_ids, $new_ids);
        }
        $actor_ids = array_unique($actor_ids);

        if (!empty($actor_ids)) {
            $stmt_a = $conn->prepare("INSERT INTO movie_actors (MovieID, ActorID) VALUES (?, ?)");
            foreach ($actor_ids as $actor_id) {
                $stmt_a->bind_param("ii", $movie_id, $actor_id);
                $stmt_a->execute();
            }
            $stmt_a->close();
        }

        $conn->commit();
        $conn->close();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return "Lỗi: " . $e->getMessage();
    }
}

function updateMovie($data) {
    $conn = getDbConnection();
    $conn->begin_transaction();

    try {
        $movie_id = $data['MovieID'];
        $title = $data['Title'];
        $description = $data['Description'];
        $duration = $data['Duration'];
        $director = $data['Director'];
        $posterUrl = !empty($data['poster_file_path']) ? $data['poster_file_path'] : ($data['PosterURL'] ?? '');
        $trailerUrl = $data['TrailerURL'];
        $status = $data['Status'];
        $releaseDate = $data['ReleaseDate'];

        // 1. Update Movies
        $sql = "UPDATE Movies SET Title=?, Description=?, Duration=?, Director=?, PosterURL=?, TrailerURL=?, Status=?, ReleaseDate=? WHERE MovieID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssssi", $title, $description, $duration, $director, $posterUrl, $trailerUrl, $status, $releaseDate, $movie_id);
        $stmt->execute();
        $stmt->close();

        // 2. Cập nhật Thể loại
        $conn->query("DELETE FROM movie_genres WHERE MovieID = $movie_id");
        if (!empty($data['genres'])) {
            $stmt_g = $conn->prepare("INSERT INTO movie_genres (MovieID, GenreID) VALUES (?, ?)");
            foreach ($data['genres'] as $genre_id) {
                $stmt_g->bind_param("ii", $movie_id, $genre_id);
                $stmt_g->execute();
            }
            $stmt_g->close();
        }

        // 3. Cập nhật Diễn viên (Cũ + Mới)
        $conn->query("DELETE FROM movie_actors WHERE MovieID = $movie_id");
        
        $actor_ids = $data['actors'] ?? [];
        if (!empty($data['new_actors'])) {
            $new_ids = processNewActors($conn, $data['new_actors']);
            $actor_ids = array_merge($actor_ids, $new_ids);
        }
        $actor_ids = array_unique($actor_ids);

        if (!empty($actor_ids)) {
            $stmt_a = $conn->prepare("INSERT INTO movie_actors (MovieID, ActorID) VALUES (?, ?)");
            foreach ($actor_ids as $actor_id) {
                $stmt_a->bind_param("ii", $movie_id, $actor_id);
                $stmt_a->execute();
            }
            $stmt_a->close();
        }

        $conn->commit();
        $conn->close();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return "Lỗi: " . $e->getMessage();
    }
}

function deleteMovie($movie_id) {
    $conn = getDbConnection();
    $conn->query("DELETE FROM movie_genres WHERE MovieID = $movie_id");
    $conn->query("DELETE FROM movie_actors WHERE MovieID = $movie_id");

    $stmt = $conn->prepare("DELETE FROM Movies WHERE MovieID = ?");
    $stmt->bind_param("i", $movie_id);

    try {
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa thành công!'];
        } else {
            return ['success' => false, 'message' => 'Xóa thất bại'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
    }
}

function searchMoviesByName($query) {
    $conn = getDbConnection();
    $movies = [];
    $search_term = "%" . $query . "%";
    $sql = "SELECT * FROM Movies WHERE Title LIKE ? ORDER BY ReleaseDate DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { $conn->close(); return []; }
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) $movies = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $movies;
}
?>