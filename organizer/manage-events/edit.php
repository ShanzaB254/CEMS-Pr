<?php
// organizer/manage-events/edit.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

$organizer_id = $_SESSION['user_id'];
$message = '';
$event = null;

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $sql = "SELECT * FROM events WHERE id = $event_id AND organizer_id = $organizer_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        die("Security Exception: Access restricted or event not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
    $event_id = intval($_POST['event_id']);
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $event_time = $conn->real_escape_string($_POST['event_time']);
    $venue = $conn->real_escape_string(trim($_POST['venue']));

    // Editing resets status to pending for Admin verification gate
    $update_sql = "UPDATE events SET 
                    title = '$title', 
                    description = '$description', 
                    event_date = '$event_date', 
                    event_time = '$event_time', 
                    venue = '$venue',
                    status = 'pending'
                   WHERE id = $event_id AND organizer_id = $organizer_id";
    
    if ($conn->query($update_sql) === TRUE) {
        header("Location: index.php?msg=updated");
        exit();
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event - CEMS</title>
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
            <h1>Configure Event Parameters</h1>
            <p>Modify location, description, or time details. <em>Saves will reset event status to pending approval.</em></p>
        </header>

        <div class="card" style="max-width: 700px;">
            <?php echo $message; ?>
            
            <?php if ($event): ?>
            <form action="edit.php?id=<?php echo $event['id']; ?>" method="POST">
                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">

                <div class="form-group">
                    <label>Event Headline Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Target Date</label>
                        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Assigned Venue/Location</label>
                    <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="update_event" class="btn btn-primary" style="background: #10b981; flex: 2;">Save Specifications</button>
                    <a href="index.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>