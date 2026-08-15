<?php
// organizer/notifications.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$organizer_id = $_SESSION['user_id'];
$message_status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_notification'])) {
    $event_id = intval($_POST['event_id']);
    $subject = $conn->real_escape_string(trim($_POST['subject']));
    $body = $conn->real_escape_string(trim($_POST['body']));

    $roster_sql = "SELECT users.id, users.email FROM registrations 
                   JOIN users ON registrations.user_id = users.id 
                   WHERE registrations.event_id = $event_id";
    
    $roster_result = $conn->query($roster_sql);
    
    if ($roster_result->num_rows > 0) {
        $recipient_count = 0;
        
        while($person = $roster_result->fetch_assoc()) {
            $recipient_id = $person['id'];
            $insert_sql = "INSERT INTO notifications (recipient_id, event_id, subject, message) 
                           VALUES ($recipient_id, $event_id, '$subject', '$body')";
            $conn->query($insert_sql);
            $recipient_count++;
        }
        
        $message_status = "<div style='background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;'>Success! Notification dispatched to the inboxes of $recipient_count registered students.</div>";
    } else {
        $message_status = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Notice: No student/staff attendees are currently registered for this event. Broadcast cancelled.</div>";
    }
}

// Fetch approved events to select from
$events_sql = "SELECT id, title FROM events WHERE organizer_id = $organizer_id AND status = 'approved'";
$events_result = $conn->query($events_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mass Broadcasts - CEMS</title>
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
            <a href="participants.php" class="nav-item">Rosters</a>
            <a href="manage-events/feedback.php" class="nav-item">Events Feedback</a>
            <a href="notifications.php" class="nav-item active">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Mass Notification Dispatcher</h1>
            <p>Blast updates, schedule modifications, or announcements directly to user inboxes.</p>
        </header>

        <div class="card" style="max-width: 650px;">
            <?php echo $message_status; ?>
            
            <form action="notifications.php" method="POST">
                <div class="form-group">
                    <label>Target Event</label>
                    <select name="event_id" required>
                        <option value="">-- Select Active Approved Event --</option>
                        <?php while($event = $events_result->fetch_assoc()): ?>
                            <option value="<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Subject Headline</label>
                    <input type="text" name="subject" required placeholder="e.g., Room Change Announcement">
                </div>

                <div class="form-group">
                    <label>Message Content</label>
                    <textarea name="body" rows="6" required placeholder="Write your broadcast contents here..."></textarea>
                </div>

                <button type="submit" name="send_notification" class="btn btn-primary" style="background: #10b981; width: 100%; margin-top: 15px;">Send Broadcast Alert</button>
            </form>
        </div>
    </main>

</div>

</body>
</html>