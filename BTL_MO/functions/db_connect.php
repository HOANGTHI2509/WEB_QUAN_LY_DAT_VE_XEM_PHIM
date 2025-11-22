<?php
// BTL_MO/functions/db_connect.php

function getDbConnection() {
    $servername = "localhost"; // ⚠️ Tên máy chủ (thường là "localhost")
    $username = "root";
    $password = "889900";
    $dbname = "cinema_db";
    $port = 3306;

    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        die("Kết nối database thất bại: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    
    return $conn;
}
?>