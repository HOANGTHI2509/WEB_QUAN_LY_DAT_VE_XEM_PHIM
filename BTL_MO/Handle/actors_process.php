<?php
// BTL_MO/Handle/actors_process.php
session_start();
require_once '../functions/actors_functions.php';
require_once '../functions/admin_gate.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add':
                $res = addActor($_POST);
                if ($res === true) header("location: ../View/admin/actors.php?success=add");
                else throw new Exception($res);
                break;
            case 'update':
                $res = updateActor($_POST);
                if ($res === true) header("location: ../View/admin/actors.php?success=update");
                else throw new Exception($res);
                break;
            case 'delete':
                $res = deleteActor($_POST['actor_id']);
                if ($res['success']) header("location: ../View/admin/actors.php?success=delete");
                else throw new Exception($res['message']);
                break;
        }
    } catch (Exception $e) {
        header("location: ../View/admin/actors.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../View/admin/actors.php");
}
?>