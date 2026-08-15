<?php
// organizer/participants.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$organizer_id = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Rosters - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css">
      <link rel="stylesheet" href="../assets/css/organizer.css?v=<?php echo filemtime('../assets/css/organizer.css'); ?>"> 
    <!-- <link rel="stylesheet" href="../assets/css/organizer.css"> -->
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Portal</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">Dashboard</a>
            <a href="manage-events/index.php" class="nav-item">Manage Events</a>
            <a href="participants.php" class="nav-item active">Rosters</a>
            <a href="manage-events/feedback.php" class="nav-item">Events Feedback</a>
            <a href="notifications.php" class="nav-item">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Event Registration Rosters</h1>
            <p>View detailed participant and volunteer lists for your approved events.</p>
        </header>

        <?php if ($event_id === 0): ?>
            <div class="card" style="max-width: 600px;">
                <h3 style="margin-bottom: 20px;">Select an Event</h3>
                <?php
                $events_sql = "SELECT id, title, event_date FROM events WHERE organizer_id = $organizer_id AND status = 'approved' ORDER BY event_date ASC";
                $events_result = $conn->query($events_sql);
                ?>
                <?php if ($events_result->num_rows > 0): ?>
                    <ul style="list-style: none; padding: 0;">
                        <?php while($event = $events_result->fetch_assoc()): ?>
                            <li style="margin-bottom: 10px;">
                                <a href="participants.php?event_id=<?php echo $event['id']; ?>" class="btn" style="background: #f8fafc; color: var(--text-dark); border: 1px solid var(--border-color); display: flex; justify-content: space-between; width: 100%;">
                                    <strong><?php echo htmlspecialchars($event['title']); ?></strong> 
                                    <span style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: var(--text-muted);">You have no approved events with active registrations.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <?php
            $title_query = $conn->query("SELECT title FROM events WHERE id = $event_id AND organizer_id = $organizer_id");
            if ($title_query->num_rows === 0) die("Access denied or event not found.");
            $event_title = $title_query->fetch_assoc()['title'];

            $roster_sql = "SELECT users.name, users.email, registrations.type, registrations.registered_at 
                           FROM registrations 
                           JOIN users ON registrations.user_id = users.id 
                           WHERE registrations.event_id = $event_id 
                           ORDER BY registrations.type DESC, registrations.registered_at ASC";
            $roster_result = $conn->query($roster_sql);
            ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">Roster: <?php echo htmlspecialchars($event_title); ?></h3>
                    <a href="participants.php" class="btn" style="background: #e5e7eb; color: #374151; padding: 6px 12px; font-size: 0.85rem;">&larr; Back to Events</a>
                </div>

                <?php if ($roster_result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Email Address</th>
                                <th>Registration Role</th>
                                <th>Date Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($person = $roster_result->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 500;"><?php echo htmlspecialchars($person['name']); ?></td>
                                    <td><?php echo htmlspecialchars($person['email']); ?></td>
                                    <td>
                                        <span class="badge" style="background: <?php echo $person['type'] === 'volunteer' ? '#fef3c7; color: #d97706;' : '#dbeafe; color: #1d4ed8;'; ?>">
                                            <?php echo ucfirst($person['type']); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.9em; color: var(--text-muted);"><?php echo date('M d, Y g:i A', strtotime($person['registered_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 30px 0; color: var(--text-muted);">No one has registered for this event yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>