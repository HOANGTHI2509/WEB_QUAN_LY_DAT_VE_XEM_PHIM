<?php
// BTL_MO/functions/promotions_functions.php

require_once 'db_connect.php';

/**
 * Lấy tất cả khuyến mãi
 * @return array Danh sách
 */
function getAllPromotions() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM Promotions ORDER BY EndDate DESC";
    $result = $conn->query($sql);
    $data = [];
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }
    $conn->close();
    return $data;
}

/**
 * Lấy thông tin 1 khuyến mãi
 * @param int $promo_id ID
 * @return array|null Dữ liệu
 */
function getPromotionById($promo_id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM Promotions WHERE PromotionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $promo_id);
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
 * Thêm khuyến mãi mới
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
 */
function addPromotion($data) {
    $conn = getDbConnection();
    $sql = "INSERT INTO Promotions (Code, DiscountValue, DiscountPercent, StartDate, EndDate) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiss",
        $data['code'],
        $data['discount_value'],
        $data['discount_percent'],
        $data['start_date'],
        $data['end_date']
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
 * Cập nhật khuyến mãi
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi
 */
function updatePromotion($data) {
    $conn = getDbConnection();
    $sql = "UPDATE Promotions SET Code = ?, DiscountValue = ?, DiscountPercent = ?, StartDate = ?, EndDate = ? 
            WHERE PromotionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", 
        $data['code'],
        $data['discount_value'],
        $data['discount_percent'],
        $data['start_date'],
        $data['end_date'],
        $data['promotion_id']
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
 * Xóa khuyến mãi
 * @param int $promo_id ID
 * @return array [success (bool), message (string)]
 */
function deletePromotion($promo_id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM Promotions WHERE PromotionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $promo_id);

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
            $message = 'Không thể xóa vì đã có đơn hàng áp dụng mã này.';
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