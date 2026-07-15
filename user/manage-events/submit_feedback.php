<?php
// user/manage-events/submit_feedback.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];
$message = '';

if (isset($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $verify_sql = "SELECT e.title FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.user_id = $user_id AND r.event_id = $event_id";
    $verify_result = $conn->query($verify_sql);
    if ($verify_result->num_rows == 1) {
        $event_data = $verify_result->fetch_assoc();
    } else {
        die("Invalid request.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $rating = intval($_POST['rating']);
    $comments = $conn->real_escape_string(trim($_POST['comments']));

    $check_duplicate = $conn->query("SELECT id FROM feedback WHERE user_id = $user_id AND event_id = $event_id");
    
    if ($check_duplicate->num_rows == 0) {
        $insert_sql = "INSERT INTO feedback (event_id, user_id, rating, comments) VALUES ($event_id, $user_id, $rating, '$comments')";
        if ($conn->query($insert_sql) === TRUE) {
            header("Location: my_events.php?msg=feedback_saved");
            exit();
        } else {
            $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Feedback already submitted.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback - CEMS</title>
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
            <h1>Event Experience</h1>
        </header>

        <div class="card" style="max-width: 500px;">
            <?php echo $message; ?>
            <h3 style="margin-top: 0; color: #1e3a8a; margin-bottom: 20px;">Review: <?php echo htmlspecialchars($event_data['title']); ?></h3>
            
            <form action="submit_feedback.php?event_id=<?php echo $event_id; ?>" method="POST">
                <div class="form-group">
                    <label>Overall Rating</label>
                    <select name="rating" required style="font-size: 1.1rem; padding: 12px;">
                        <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                        <option value="4">⭐⭐⭐⭐ - Good</option>
                        <option value="3">⭐⭐⭐ - Average</option>
                        <option value="2">⭐⭐ - Poor</option>
                        <option value="1">⭐ - Terrible</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Additional Comments</label>
                    <textarea name="comments" rows="5" required placeholder="What did you like? What could be improved?"></textarea>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="submit_feedback" class="btn btn-primary" style="background: #3b82f6; flex: 2;">Submit Review</button>
                    <a href="my_events.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>