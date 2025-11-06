<?php
// BTL_MO/Handle/login_process.php
// [Dựa trên file bạn đã cung cấp]

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
// Gọi file kết nối CSDL
require_once '../functions/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Kiểm tra đầu vào
    if (empty($email) || empty($password)) {
        header("location: ../View/user/login.php?error=empty");
        exit;
    }

    $conn = null;
    $stmt = null;
    
    try {
        // 2. Lấy kết nối CSDL
        $conn = getDbConnection();

        // 3. Truy vấn người dùng
        $sql = "SELECT UserID, Email, PasswordHash, FullName, Role FROM Users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // 4. Xác thực mật khẩu
            if (password_verify($password, $user['PasswordHash'])) {
                
                // MẬT KHẨU ĐÚNG
                session_regenerate_id(true); // Tạo session ID mới

                // 5. Lưu thông tin vào SESSION
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['email'] = $user['Email'];
                $_SESSION['full_name'] = $user['FullName'];
                $_SESSION['role'] = $user['Role'];

                // 6. PHÂN LUỒNG: Chuyển hướng dựa trên vai trò
                if ($user['Role'] == 'Admin') {
                    // Nếu là Admin, đến trang dashboard
                    header("location: ../View/admin/dashboard.php");
                } else {
                    // Nếu là User, về trang chủ
                    header("location: ../View/user/index.php");
                }
                exit; // Dừng script sau khi chuyển hướng

            } else {
                // MẬT KHẨU SAI
                header("location: ../View/user/login.php?error=invalid");
                exit;
            }
        } else {
            // EMAIL KHÔNG TỒN TẠI
            header("location: ../View/user/login.php?error=invalid");
            exit;
        }

    } catch (Exception $e) {
        // Lỗi nghiêm trọng (ví dụ: không kết nối được CSDL)
        die("Lỗi CSDL: " . $e->getMessage());
    } finally {
        if ($stmt) $stmt->close();
        if ($conn) $conn->close();
    }

} else {
    // Nếu không phải là POST, đá về trang đăng nhập
    header("location: ../View/user/login.php");
    exit;
}
?>