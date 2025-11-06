<?php
// BTL_MO/functions/users_functions.php

require_once 'db_connect.php';

/**
 * Lấy tất cả người dùng
 * @return array Danh sách người dùng
 */
function getAllUsers() {
    $conn = getDbConnection();
    $users = [];
    
    $sql = "SELECT UserID, FullName, Email, PhoneNumber, Role, CreatedAt 
            FROM Users 
            ORDER BY UserID DESC";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $conn->close();
    return $users;
}

/**
 * Lấy thông tin 1 người dùng
 * @param int $user_id ID người dùng
 * @return array|null Dữ liệu người dùng
 */
function getUserById($user_id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $user = null;
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    }
    
    $stmt->close();
    $conn->close();
    return $user;
}

/**
 * Kiểm tra xem email đã tồn tại chưa (khi Sửa)
 * @param string $email Email cần kiểm tra
 * @param int $exclude_user_id Bỏ qua ID người dùng này
 * @return bool True nếu email đã tồn tại
 */
function isEmailRegistered($email, $exclude_user_id = 0) {
    $conn = getDbConnection();
    $sql = "SELECT UserID FROM Users WHERE Email = ? AND UserID != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $exclude_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

/**
 * Thêm người dùng mới (dùng cho Admin)
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function addUser($data) {
    $conn = getDbConnection();

    $full_name = $data['FullName'] ?? '';
    $email = $data['Email'] ?? '';
    $phone = $data['PhoneNumber'] ?? null;
    $password = $data['Password'] ?? '';
    $role = $data['Role'] ?? 'User';

    if (empty($full_name) || empty($email) || empty($password)) {
        return "Tên, email, và mật khẩu là bắt buộc.";
    }
    
    if (isEmailRegistered($email)) {
        return "Email này đã tồn tại.";
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Users (FullName, Email, PhoneNumber, PasswordHash, Role) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $full_name, $email, $phone, $hashed_password, $role);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return "Thêm người dùng thất bại: " . $error;
    }
}

/**
 * Cập nhật người dùng (dùng cho Admin)
 * @param array $data Dữ liệu từ $_POST
 * @return bool|string True nếu thành công, chuỗi lỗi nếu thất bại
 */
function updateUser($data) {
    $conn = getDbConnection();
    
    $user_id = $data['UserID'];
    $full_name = $data['FullName'] ?? '';
    $email = $data['Email'] ?? '';
    $phone = $data['PhoneNumber'] ?? null;
    $role = $data['Role'] ?? 'User';
    $new_password = $data['Password'] ?? ''; // Mật khẩu mới

    if (empty($full_name) || empty($email)) {
        return "Tên và email là bắt buộc.";
    }
    
    if (isEmailRegistered($email, $user_id)) {
        return "Email này đã được sử dụng bởi người dùng khác.";
    }

    // Kiểm tra xem admin có muốn đổi mật khẩu không
    if (!empty($new_password)) {
        // Nếu có, cập nhật mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ?, PasswordHash = ? 
                WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $full_name, $email, $phone, $role, $hashed_password, $user_id);
    } else {
        // Nếu không, giữ nguyên mật khẩu cũ
        $sql = "UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ? 
                WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $role, $user_id);
    }

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
 * Xóa người dùng
 * @param int $user_id ID người dùng
 * @return array [success (bool), message (string)]
 */
function deleteUser($user_id) {
    $conn = getDbConnection();
    
    // (An toàn: Không cho phép xóa admin có ID = 1)
    if ($user_id == 1) {
        return ['success' => false, 'message' => 'Không thể xóa tài khoản Admin gốc.'];
    }

    $sql = "DELETE FROM Users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    try {
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = 'Xóa người dùng thành công!';
                $success = true;
            } else {
                $message = 'Không tìm thấy người dùng để xóa.';
                $success = false;
            }
        } else {
            $message = 'Xóa thất bại: ' . $stmt->error;
            $success = false;
        }
    } catch (mysqli_sql_exception $e) {
        // Bắt lỗi khóa ngoại (nếu người dùng đã đặt vé)
        if ($e->getCode() == 1451) { 
            $message = 'Không thể xóa người dùng này vì họ đã có đơn đặt vé. Bạn có thể khóa tài khoản (chức năng nâng cao).';
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