<?php
// user/manage-events/register_event.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];
$message = '';
$event_data = null;

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $result = $conn->query("SELECT title, event_date, venue FROM events WHERE id = $event_id AND status = 'approved'");
    if ($result->num_rows == 1) {
        $event_data = $result->fetch_assoc();
    } else {
        die("Event unavailable.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_registration'])) {
    $type = $conn->real_escape_string($_POST['type']);
    $check = $conn->query("SELECT id FROM registrations WHERE user_id = $user_id AND event_id = $event_id");
    
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO registrations (event_id, user_id, type) VALUES ($event_id, $user_id, '$type')";
        if ($conn->query($sql) === TRUE) {
            header("Location: ../index.php?msg=registered");
            exit();
        } else {
            $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Already registered.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Registration - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/user.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><h2>Campus Life</h2></div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Discover Events</a>
            <a href="my_events.php" class="nav-item">My Schedule</a>
            <a href="../notifications.php" class="nav-item">Inbox</a>
        </nav>
        <div class="sidebar-footer"><a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a></div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Secure Your Spot</h1>
        </header>

        <div class="card" style="max-width: 500px;">
            <?php echo $message; ?>
            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid var(--border-color);">
                <h3 style="margin-top: 0; color: #1e3a8a;"><?php echo htmlspecialchars($event_data['title']); ?></h3>
                <strong>Date:</strong> <?php echo date('F d, Y', strtotime($event_data['event_date'])); ?><br>
                <strong>Venue:</strong> <?php echo htmlspecialchars($event_data['venue']); ?>
            </div>
            
            <form action="register_event.php?id=<?php echo $event_id; ?>" method="POST">
                <div class="form-group">
                    <label>How would you like to participate?</label>
                    <select name="type" required>
                        <option value="attendee">Standard Attendee</option>
                        <option value="volunteer">Event Volunteer</option>
                    </select>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="confirm_registration" class="btn btn-primary" style="background: #3b82f6; flex: 2;">Confirm Registration</button>
                    <a href="../index.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>