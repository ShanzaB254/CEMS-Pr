<?php
// admin/announcements.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';
$admin_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_announcement'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $body = $conn->real_escape_string(trim($_POST['message']));
    
    if (!empty($title) && !empty($body)) {
        $sql = "INSERT INTO announcements (admin_id, title, message) VALUES ($admin_id, '$title', '$body')";
        if ($conn->query($sql) === TRUE) {
            $message = "<div style='background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;'>Announcement successfully published site-wide!</div>";
        } else {
            $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Database Error: " . $conn->error . "</div>";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM announcements WHERE id = $delete_id");
    header("Location: announcements.php?msg=deleted");
    exit();
}

$result = $conn->query("SELECT id, title, message, created_at FROM announcements ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">Dashboard</a>
            <a href="manage-users/manage_users.php" class="nav-item">Manage Users</a>
            <a href="announcements.php" class="nav-item active">Announcements</a>
            <a href="profile.php" class="nav-item">My Profile</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Campus Announcements</h1>
            <p>Publish broadcasts, reminders, and alerts directly to the public landing dashboard.</p>
        </header>

        <div style="display: grid; grid-template-columns: 1.2fr 2fr; gap: 30px;">
            
            <div class="card" style="align-self: flex-start;">
                <h3 style="margin-bottom: 15px;">Publish Broadcast</h3>
                <?php echo $message; ?>
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div style='background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Deleted successfully.</div>"; ?>
                
                <form action="announcements.php" method="POST">
                    <div class="form-group">
                        <label>Headline Title</label>
                        <input type="text" name="title" required placeholder="e.g., Spring Registration Opens">
                    </div>
                    <div class="form-group">
                        <label>Message Content</label>
                        <textarea name="message" rows="5" required placeholder="Details about this announcement..."></textarea>
                    </div>
                    <button type="submit" name="post_announcement" class="btn btn-primary" style="width: 100%;">Publish to Homepage</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 15px;">Active Broadcasts</h3>
                <?php if ($result->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Headline</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td style="white-space: nowrap; font-size: 0.9em;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td>
                                        <a href="announcements.php?delete_id=<?php echo $row['id']; ?>" class="btn trigger-modal" data-action="delete" style="background: var(--danger); color: white; padding: 6px 12px; font-size: 0.85rem;">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; padding: 30px 0; color: var(--text-muted);">No announcements have been published yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

</div>

<?php include '../includes/ui-components/action_modal.php'; ?>
</body>
</html>