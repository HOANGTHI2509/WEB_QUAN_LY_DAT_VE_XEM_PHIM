<?php
// BTL_MO/Handle/seattypes_process.php
session_start();
require_once '../functions/seattypes_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add':
                $res = addSeatType($_POST);
                if ($res === true) header("location: ../View/admin/seat_types.php?success=add");
                else throw new Exception($res);
                break;
            case 'update':
                $res = updateSeatType($_POST);
                if ($res === true) header("location: ../View/admin/seat_types.php?success=update");
                else throw new Exception($res);
                break;
            case 'delete':
                $res = deleteSeatType($_POST['seat_type_id']);
                if ($res['success']) header("location: ../View/admin/seat_types.php?success=delete");
                else throw new Exception($res['message']);
                break;
        }
    } catch (Exception $e) {
        header("location: ../View/admin/seat_types.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../View/admin/seat_types.php");
}
?>