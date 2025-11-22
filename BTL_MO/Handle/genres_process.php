<?php
// BTL_MO/Handle/genres_process.php
session_start();
require_once '../functions/genres_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add':
                $res = addGenre($_POST);
                if ($res === true) {
                    header("location: ../View/admin/genres.php?success=add");
                } else {
                    throw new Exception($res);
                }
                break;

            case 'update':
                $res = updateGenre($_POST);
                if ($res === true) {
                    header("location: ../View/admin/genres.php?success=update");
                } else {
                    throw new Exception($res);
                }
                break;

            case 'delete':
                if (isset($_POST['genre_id'])) {
                    $res = deleteGenre($_POST['genre_id']);
                    if ($res['success']) {
                        header("location: ../View/admin/genres.php?success=delete");
                    } else {
                        throw new Exception($res['message']);
                    }
                }
                break;
                
            default:
                throw new Exception("Hành động không hợp lệ");
        }
    } catch (Exception $e) {
        header("location: ../View/admin/genres.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../View/admin/genres.php");
}
?>