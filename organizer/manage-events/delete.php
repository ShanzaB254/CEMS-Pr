<?php
// organizer/manage-events/delete.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $organizer_id = $_SESSION['user_id'];

    // SECURE QUERY: Ensure target event is owned by active session ID
    $sql = "DELETE FROM events WHERE id = $event_id AND organizer_id = $organizer_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=canceled");
    } else {
        echo "Database Exception: " . $conn->error;
    }
} else {
    header("Location: index.php");
}
exit();
?>