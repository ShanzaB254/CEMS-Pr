<?php
// admin/manage-users/delete.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

if (isset($_GET['id'])) {
    $target_user_id = intval($_GET['id']);
    $current_admin_id = $_SESSION['user_id'];

    if ($target_user_id === $current_admin_id) {
        die("Security Exception: You cannot delete your active administrative session.");
    }

    $sql = "DELETE FROM users WHERE id = $target_user_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_users.php?msg=deleted");
    } else {
        echo "Database Exception: " . $conn->error;
    }
} else {
    header("Location: manage_users.php");
}
exit();
?>