<?php
// organizer/manage-events/end_event.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $organizer_id = $_SESSION['user_id'];

    // Securely update the event to ended
    $sql = "UPDATE events SET is_ended = TRUE WHERE id = $event_id AND organizer_id = $organizer_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=ended");
    } else {
        echo "Database Exception: " . $conn->error;
    }
} else {
    header("Location: index.php");
}
exit();
?>