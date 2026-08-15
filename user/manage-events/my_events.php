<?php
// user/manage-events/my_events.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];

// Fetch Upcoming Events (Not Ended)
$upcoming_sql = "SELECT e.id, e.title, e.event_date, e.event_time, e.venue, r.type, u.name AS organizer_name 
                 FROM registrations r 
                 JOIN events e ON r.event_id = e.id 
                 JOIN users u ON e.organizer_id = u.id
                 WHERE r.user_id = $user_id AND e.is_ended = FALSE 
                 ORDER BY e.event_date ASC";
$upcoming_result = $conn->query($upcoming_sql);

// Fetch Past Events (Manually Ended by Organizer)
$past_sql = "SELECT e.id, e.title, e.event_date 
             FROM registrations r 
             JOIN events e ON r.event_id = e.id
             WHERE r.user_id = $user_id AND e.is_ended = TRUE 
             ORDER BY e.event_date DESC";
$past_result = $conn->query($past_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Schedule - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/user.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><h2>Campus Life</h2></div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Discover Events</a>
            <a href="my_events.php" class="nav-item active">My Schedule</a>
            <a href="../notifications.php" class="nav-item">Inbox</a>
        </nav>
        <div class="sidebar-footer"><a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a></div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>My Event Itinerary</h1>
            <p>Track your upcoming registrations and download entry passes.</p>
        </header>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'feedback_saved'): ?>
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px;">Thank you for your feedback!</div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 30px;">
            <h2 style="font-size: 1.25rem; color: #1e3a8a; margin-bottom: 20px;">Upcoming Schedule</h2>
            <?php if ($upcoming_result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Details</th>
                            <th>Date & Time</th>
                            <th>Role</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $upcoming_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong><br><small style="color: var(--text-muted);"><?php echo htmlspecialchars($row['venue']); ?></small></td>
                                <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?><br><small style="color: var(--text-muted);"><?php echo date('h:i A', strtotime($row['event_time'])); ?></small></td>
                                <td><span style="background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 20px; font-size: 0.85em; font-weight: bold; text-transform: uppercase;"><?php echo htmlspecialchars($row['type']); ?></span></td>
                                <td><a href="pass.php?event_id=<?php echo $row['id']; ?>" class="btn" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 0.85rem;">View Pass</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">You have no upcoming events.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 style="font-size: 1.25rem; color: var(--text-muted); margin-bottom: 20px;">Past Events</h2>
            <?php if ($past_result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date Attended</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $past_result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                                <td><a href="submit_feedback.php?event_id=<?php echo $row['id']; ?>" class="btn" style="background: var(--secondary); padding: 6px 12px; font-size: 0.85rem;">Leave Feedback</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">No past event attendance records found.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>