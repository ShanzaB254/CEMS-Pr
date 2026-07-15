<?php
// organizer/manage-events/index.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$organizer_id = $_SESSION['user_id'];

// Pull organizer events, mapping attendee and volunteer counts cleanly with subqueries
$sql = "SELECT id, title, event_date, event_time, venue, status,
        (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND type = 'attendee') AS attendee_count,
        (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND type = 'volunteer') AS volunteer_count
        FROM events 
        WHERE organizer_id = $organizer_id 
        ORDER BY event_date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Events - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/organizer.css">
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Portal</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Dashboard</a>
            <a href="index.php" class="nav-item active">Manage Events</a>
            <a href="../participants.php" class="nav-item">Rosters</a>
            <a href="feedback.php" class="nav-item">Events Feedback</a>
            <a href="../notifications.php" class="nav-item">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>My Event Portfolio</h1>
                <p>Track rosters, manage event scheduling parameters, and submit new proposals.</p>
            </div>
            <a href="add.php" class="btn btn-primary" style="background: #10b981;">+ Design Event Request</a>
        </header>

        <div class="card">
            <?php if (isset($_GET['msg'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: 15px; font-weight: 500;">
                    Event database record successfully <?php echo htmlspecialchars($_GET['msg']); ?>!
                </div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Details</th>
                            <th>Date & Time</th>
                            <th>Venue</th>
                            <th>Roster (Att./Vol.)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?><br><small style="color: var(--text-muted);"><?php echo date('h:i A', strtotime($row['event_time'])); ?></small></td>
                                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                                <td>
                                    <strong><?php echo $row['attendee_count']; ?></strong> Attendees / 
                                    <strong><?php echo $row['volunteer_count']; ?></strong> Volunteers
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn" style="background: var(--secondary); color: white; padding: 6px 12px; font-size: 0.85rem;">Edit</a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn trigger-modal" data-action="delete" style="background: var(--danger); color: white; padding: 6px 12px; font-size: 0.85rem;">Cancel</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px 0; color: var(--text-muted);">You have not created any event records yet.</p>
            <?php endif; ?>
        </div>
    </main>

</div>

<?php include '../../includes/ui-components/action_modal.php'; ?>
</body>
</html>