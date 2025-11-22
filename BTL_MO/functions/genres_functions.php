<?php
// BTL_MO/functions/genres_functions.php
require_once 'db_connect.php';

function getAllGenres() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM Genres ORDER BY Name ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getGenreById($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM Genres WHERE GenreID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addGenre($data) {
    $conn = getDbConnection();
    $name = trim($data['name']);
    if (empty($name)) return "Tên thể loại không được để trống.";

    $stmt = $conn->prepare("INSERT INTO Genres (Name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function updateGenre($data) {
    $conn = getDbConnection();
    $id = $data['genre_id'];
    $name = trim($data['name']);
    
    $stmt = $conn->prepare("UPDATE Genres SET Name = ? WHERE GenreID = ?");
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function deleteGenre($id) {
    $conn = getDbConnection();
    // Xóa dữ liệu bảng trung gian trước
    $conn->query("DELETE FROM movie_genres WHERE GenreID = $id");
    
    $stmt = $conn->prepare("DELETE FROM Genres WHERE GenreID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) return ['success' => true];
    return ['success' => false, 'message' => $stmt->error];
}
?>