<?php
// organizer/manage-events/add.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $organizer_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $event_time = $conn->real_escape_string($_POST['event_time']);
    $venue = $conn->real_escape_string(trim($_POST['venue']));

    if (empty($title) || empty($description) || empty($event_date) || empty($event_time) || empty($venue)) {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>All parameters are required.</div>";
    } else {
        $sql = "INSERT INTO events (organizer_id, title, description, event_date, event_time, venue) 
                VALUES ('$organizer_id', '$title', '$description', '$event_date', '$event_time', '$venue')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?msg=submitted");
            exit();
        } else {
            $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Database Exception: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Propose New Event - CEMS</title>
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
            <a href="../notifications.php" class="nav-item">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Propose Event Request</h1>
            <p>Draft scheduling details and locations to submit to system administrators for verification.</p>
        </header>

        <div class="card" style="max-width: 700px;">
            <?php echo $message; ?>
            
            <form action="add.php" method="POST">
                <div class="form-group">
                    <label>Event Headline Title</label>
                    <input type="text" name="title" required placeholder="e.g., Annual Tech Symposium">
                </div>

                <div class="form-group">
                    <label>Event Description</label>
                    <textarea name="description" rows="4" required placeholder="Outline event parameters, schedules, or target demographics..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Target Date</label>
                        <input type="date" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="event_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Assigned Venue/Location</label>
                    <input type="text" name="venue" required placeholder="e.g., Seminar Auditorium B">
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="create_event" class="btn btn-primary" style="background: #10b981; flex: 2;">Submit for Approval</button>
                    <a href="index.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </main>

</div>

</body>
</html>