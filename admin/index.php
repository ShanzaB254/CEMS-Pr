<?php
// admin/index.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';

// Fetch System Statistics to populate the beautiful cards
$user_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$pending_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'pending'")->fetch_assoc()['count'];
$total_announcements = $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'];

// Fetch Pending Events for the table
$sql = "SELECT events.id, events.title, events.event_date, events.venue, events.status, users.name AS organizer_name 
        FROM events 
        JOIN users ON events.organizer_id = users.id 
        WHERE events.status = 'pending' 
        ORDER BY events.created_at ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css?v=<?php echo filemtime('../assets/css/common.css'); ?>">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo filemtime('../assets/css/admin.css'); ?>"> 
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">Dashboard</a>
            <a href="manage-users/manage_users.php" class="nav-item">Manage Users</a>
            <a href="announcements.php" class="nav-item">Announcements</a>
            <a href="profile.php" class="nav-item">My Profile</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
                <p>Here is what's happening on campus today.</p>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Registered Users</div>
                <div class="stat-value"><?php echo $user_count; ?></div>
            </div>
            <div class="stat-card" style="border-left-color: var(--warning);">
                <div class="stat-title">Pending Approvals</div>
                <div class="stat-value"><?php echo $pending_events; ?></div>
            </div>
            <div class="stat-card" style="border-left-color: var(--success);">
                <div class="stat-title">Active Announcements</div>
                <div class="stat-value"><?php echo $total_announcements; ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px;">Pending Event Requests</h2>
            
            <?php if (isset($_GET['msg'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: 15px; font-weight: 500;">
                    Action completed successfully!
                </div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Organizer</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['organizer_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['venue']); ?></td>
                                <td>
                                    <a href="process_event.php?id=<?php echo $row['id']; ?>&action=approve" class="btn btn-primary trigger-modal" data-action="approve" style="background: var(--success); padding: 6px 12px; font-size: 0.85rem; margin-right: 5px;">Approve</a>
                                    <a href="process_event.php?id=<?php echo $row['id']; ?>&action=reject" class="btn btn-primary trigger-modal" data-action="reject" style="background: var(--danger); padding: 6px 12px; font-size: 0.85rem;">Reject</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px 0; color: var(--text-muted);">No pending event requests at this time. All caught up!</p>
            <?php endif; ?>
        </div>
    </main>

</div>

<?php include '../includes/ui-components/action_modal.php'; ?>
</body>
</html>