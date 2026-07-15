<?php
// admin/process_event.php
session_start();

// SECURITY: Only Admins can access this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';

// Check if we have both an ID and an action in the URL
if (isset($_GET['id']) && isset($_GET['action'])) {
    $event_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Determine the new status based on the action
    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    } else {
        die("Invalid action specified.");
    }

    // Update the database securely
    $sql = "UPDATE events SET status = '$new_status' WHERE id = $event_id AND status = 'pending'";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect back to the admin dashboard with a success message
        header("Location: index.php?msg=$new_status");
    } else {
        echo "Error processing event: " . $conn->error;
    }
} else {
    // If accessed directly without parameters, send them back
    header("Location: index.php");
}
exit();
?>