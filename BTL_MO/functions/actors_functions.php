<?php
// BTL_MO/functions/actors_functions.php
require_once 'db_connect.php';

function getAllActors() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM Actors ORDER BY Name ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getActorById($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM Actors WHERE ActorID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addActor($data) {
    $conn = getDbConnection();
    $name = trim($data['name']);
    
    $stmt = $conn->prepare("INSERT INTO Actors (Name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function updateActor($data) {
    $conn = getDbConnection();
    $id = $data['actor_id'];
    $name = trim($data['name']);
    
    $stmt = $conn->prepare("UPDATE Actors SET Name = ? WHERE ActorID = ?");
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) return true;
    return "Lỗi: " . $stmt->error;
}

function deleteActor($id) {
    $conn = getDbConnection();
    $conn->query("DELETE FROM movie_actors WHERE ActorID = $id");
    
    $stmt = $conn->prepare("DELETE FROM Actors WHERE ActorID = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) return ['success' => true];
    return ['success' => false, 'message' => $stmt->error];
}
?>