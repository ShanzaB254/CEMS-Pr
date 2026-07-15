<?php
// organizer/manage-events/feedback.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$organizer_id = $_SESSION['user_id'];

$sql = "SELECT events.title, users.name AS attendee_name, feedback.rating, feedback.comments, feedback.submitted_at 
        FROM feedback 
        JOIN events ON feedback.event_id = events.id 
        JOIN users ON feedback.user_id = users.id 
        WHERE events.organizer_id = $organizer_id 
        ORDER BY feedback.submitted_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Feedback - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/organizer.css">
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><h2>CEMS Portal</h2></div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Dashboard</a>
            <a href="index.php" class="nav-item">Manage Events</a>
            <a href="../participants.php" class="nav-item">Rosters</a>
            <a href="feedback.php" class="nav-item active">Events Feedback</a>
            <a href="../notifications.php" class="nav-item">Mass Broadcasts</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Post-Event Feedback</h1>
            <p>Review student ratings and comments to improve future campus activities.</p>
        </header>

        <div class="card">
            <?php if ($result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Attendee</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['attendee_name']); ?></td>
                                <td style="color: #f59e0b; font-size: 1.1rem; letter-spacing: 2px;">
                                    <?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?>
                                </td>
                                <td style="font-style: italic; color: var(--text-muted);">"<?php echo htmlspecialchars($row['comments']); ?>"</td>
                                <td style="font-size: 0.85em;"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 30px 0; color: var(--text-muted);">No feedback has been submitted for any of your events yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>