<?php
// BTL_MO/functions/food_functions.php

require_once 'db_connect.php';

/**
 * Lấy tất cả đồ ăn/combo
 * @return array Danh sách
 */
function getAllFoodCombos() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM FoodCombos ORDER BY Name";
    $result = $conn->query($sql);
    $data = [];
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }
    $conn->close();
    return $data;
}

/**
 * Lấy thông tin 1 món
 * @param int $food_id ID món
 * @return array|null Dữ liệu món
 */
function getFoodComboById($food_id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM FoodCombos WHERE FoodID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = null;
    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
    }
    $stmt->close();
    $conn->close();
    return $data;
}

/**
 * Thêm món mới
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
 */
function addFoodCombo($data) {
    $conn = getDbConnection();
    $sql = "INSERT INTO FoodCombos (Name, Description, Price, ImageURL) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", 
        $data['name'], 
        $data['description'], 
        $data['price'], 
        $data['image_url']
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Thêm thất bại: " . $error;
    }
}

/**
 * Cập nhật món
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
 */
function updateFoodCombo($data) {
    $conn = getDbConnection();
    $sql = "UPDATE FoodCombos SET Name = ?, Description = ?, Price = ?, ImageURL = ? 
            WHERE FoodID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", 
        $data['name'], 
        $data['description'], 
        $data['price'], 
        $data['image_url'],
        $data['food_id']
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
 * Xóa món
 * @param int $food_id ID món
 * @return array [success (bool), message (string)]
 */
function deleteFoodCombo($food_id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM FoodCombos WHERE FoodID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $food_id);

    try {
        if ($stmt->execute()) {
            $message = 'Xóa thành công!';
            $success = true;
        } else {
            $message = 'Xóa thất bại: ' . $stmt->error;
            $success = false;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) { 
            $message = 'Không thể xóa món này vì đã có người đặt.';
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