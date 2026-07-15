<?php
// organizer/index.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$organizer_id = $_SESSION['user_id'];

// Fetch Organizer Stats
$event_count = $conn->query("SELECT COUNT(*) as count FROM events WHERE organizer_id = $organizer_id")->fetch_assoc()['count'];

$attendee_count = $conn->query("SELECT COUNT(*) as count FROM registrations r 
                                JOIN events e ON r.event_id = e.id 
                                WHERE e.organizer_id = $organizer_id AND r.type = 'attendee'")->fetch_assoc()['count'];

$volunteer_count = $conn->query("SELECT COUNT(*) as count FROM registrations r 
                                 JOIN events e ON r.event_id = e.id 
                                 WHERE e.organizer_id = $organizer_id AND r.type = 'volunteer'")->fetch_assoc()['count'];

// Fetch Approved Upcoming Events for visual calendar display
$sql = "SELECT title, event_date, event_time, venue FROM events 
        WHERE organizer_id = $organizer_id AND status = 'approved' AND event_date >= CURDATE() 
        ORDER BY event_date ASC LIMIT 5";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/organizer.css">
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Portal</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">Dashboard</a>
            <a href="manage-events/index.php" class="nav-item">Manage Events</a>
            <a href="participants.php" class="nav-item">Rosters</a>
            <a href="manage-events/feedback.php" class="nav-item">Events Feedback</a>
            <a href="notifications.php" class="nav-item">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Welcome Back, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <p>Coordinate your campus events, volunteers, and notifications from one control interface.</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">My Created Events</div>
                <div class="stat-value"><?php echo $event_count; ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #3b82f6;">
                <div class="stat-title">Registered Attendees</div>
                <div class="stat-value"><?php echo $attendee_count; ?></div>
            </div>
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <div class="stat-title">Active Volunteers</div>
                <div class="stat-value"><?php echo $volunteer_count; ?></div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Upcoming Schedule Timeline</h3>
            <?php if ($result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Venue Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('F d, Y', strtotime($row['event_date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($row['event_time'])); ?></td>
                                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 30px 0; color: var(--text-muted);">No upcoming approved events scheduled. Go to <a href="manage-events/add.php">Create an Event</a> to list a new one!</p>
            <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>