<?php
// BTL_MO/Handle/register_process.php
// [Dựa trên file bạn đã cung cấp]

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../functions/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = null;

    try {
        // 1. Lấy dữ liệu
        $full_name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirmPassword']); 

        // 2. KIỂM TRA DỮ LIỆU
        if (empty($full_name) || empty($email) || empty($phone_number) || empty($password) || empty($confirm_password)) {
            header("location: ../View/user/register.php?error=empty");
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("location: ../View/user/register.php?error=email_invalid");
            exit;
        }
        if (strlen($password) < 6) {
            header("location: ../View/user/register.php?error=password_short");
            exit;
        }
        if ($password !== $confirm_password) {
            header("location: ../View/user/register.php?error=password_mismatch");
            exit;
        }

        // 3. KIỂM TRA EMAIL TỒN TẠI
        $conn = getDbConnection();
        $sql_check = "SELECT UserID FROM Users WHERE Email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $stmt_check->close();
            header("location: ../View/user/register.php?error=email_exists");
            exit;
        }
        $stmt_check->close();

        // 4. BĂM MẬT KHẨU
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 5. THÊM VÀO CƠ SỞ DỮ LIỆU
        $role = "User"; // Mặc định vai trò là User

        $sql_insert = "INSERT INTO Users (FullName, Email, PhoneNumber, PasswordHash, Role) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        
        $stmt_insert->bind_param("sssss", $full_name, $email, $phone_number, $hashed_password, $role);
        
        if ($stmt_insert->execute()) {
            // Đăng ký thành công, chuyển về trang login
            header("location: ../View/user/login.php?success=registered");
            exit;
        } else {
            // Lỗi không xác định
            header("location: ../View/user/register.php?error=unknown");
            exit;
        }

    } catch (Exception $e) {
        die("Lỗi: " . $e->getMessage());
    } finally {
        if (isset($stmt_check)) $stmt_check->close();
        if (isset($stmt_insert)) $stmt_insert->close();
        if ($conn) $conn->close();
    }

} else {
    // Nếu không phải là POST, đá về trang đăng ký
    header("location: ../View/user/register.php");
    exit;
}
?>