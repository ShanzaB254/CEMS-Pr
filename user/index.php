<?php
// user/index.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];

// Fetch APPROVED events the user is NOT registered for
$sql = "SELECT e.id, e.title, e.description, e.event_date, e.event_time, e.venue, u.name AS organizer_name 
        FROM events e
        JOIN users u ON e.organizer_id = u.id
        WHERE e.status = 'approved' AND e.event_date >= CURDATE()
        AND e.id NOT IN (SELECT event_id FROM registrations WHERE user_id = $user_id)
        ORDER BY e.event_date ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/user.css">
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Campus Life</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">Discover Events</a>
            <a href="manage-events/my_events.php" class="nav-item">My Schedule</a>
            <a href="notifications.php" class="nav-item">Inbox</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
            <p>Discover upcoming workshops, seminars, and activities happening around campus.</p>
        </header>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 500;">
                Registration confirmed! Your spot has been secured.
            </div>
        <?php endif; ?>

        <div class="event-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="event-card">
                        <h3 style="color: #1e3a8a; margin: 0; font-size: 1.25rem;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        
                        <div class="event-meta">
                            📅 <?php echo date('M d, Y', strtotime($row['event_date'])); ?> at <?php echo date('h:i A', strtotime($row['event_time'])); ?><br>
                            📍 <?php echo htmlspecialchars($row['venue']); ?><br>
                            🎓 Hosted by: <strong><?php echo htmlspecialchars($row['organizer_name']); ?></strong>
                        </div>
                        
                        <div class="event-desc">
                            <?php echo htmlspecialchars(substr($row['description'], 0, 120)) . '...'; ?>
                        </div>
                        
                        <a href="register.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="background: #3b82f6; width: 100%;">Register Now</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: white; border-radius: 12px; border: 1px dashed var(--border-color);">
                    <h3 style="color: var(--text-muted);">You're all caught up!</h3>
                    <p style="color: var(--text-muted); margin: 0;">There are no new upcoming events available for registration at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>