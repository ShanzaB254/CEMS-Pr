<?php
// user/notifications.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];

// Mark read
$conn->query("UPDATE notifications SET is_read = TRUE WHERE recipient_id = $user_id AND is_read = FALSE");

$sql = "SELECT n.subject, n.message, n.sent_at, e.title AS event_title, u.name AS organizer_name 
        FROM notifications n
        JOIN events e ON n.event_id = e.id
        JOIN users u ON e.organizer_id = u.id
        WHERE n.recipient_id = $user_id 
        ORDER BY n.sent_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Inbox - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><h2>Campus Life</h2></div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">Discover Events</a>
            <a href="manage-events/my_events.php" class="nav-item">My Schedule</a>
            <a href="notifications.php" class="nav-item active">Inbox</a>
        </nav>
        <div class="sidebar-footer"><a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a></div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Event Notifications</h1>
            <p>Important updates and broadcasts from your organizers.</p>
        </header>

        <div style="max-width: 800px;">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card" style="margin-bottom: 20px; border-left: 4px solid #3b82f6;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px;">
                            <span style="font-size: 0.9rem; color: var(--text-muted);">From: <strong><?php echo htmlspecialchars($row['organizer_name']); ?></strong> (<?php echo htmlspecialchars($row['event_title']); ?>)</span>
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('M d, Y - h:i A', strtotime($row['sent_at'])); ?></span>
                        </div>
                        <h3 style="color: #1e3a8a; margin-bottom: 10px;"><?php echo htmlspecialchars($row['subject']); ?></h3>
                        <p style="color: var(--text-dark); margin: 0;"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 40px;">
                    <p style="color: var(--text-muted); margin: 0;">Your inbox is clear. No new messages.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>