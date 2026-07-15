<?php
// admin/manage-users/process.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $target_id = intval($_GET['id']);
    
    if ($_GET['action'] === 'approve') {
        $sql = "UPDATE users SET is_verified = 1 WHERE id = $target_id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_users.php?msg=approved");
        } else {
            echo "Database Error: " . $conn->error;
        }
    }
}
exit();
?>