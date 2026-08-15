<?php
// admin/manage-users/index.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

$current_admin_id = $_SESSION['user_id'];

// Fetch all users EXCEPT the active admin
$sql = "SELECT id, name, email, role, is_verified FROM users WHERE id != $current_admin_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css?v=<?php echo filemtime('../../assets/css/admin.css')">
     <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo filemtime('../../assets/css/admin.css')"> 
    
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Dashboard</a>
            <a href="manage_users.php" class="nav-item active">Manage Users</a>
            <a href="../announcements.php" class="nav-item">Announcements</a>
            <a href="../profile.php" class="nav-item">My Profile</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>User Management Directory</h1>
                <p>Monitor, approve, or modify system user accounts.</p>
            </div>
            <a href="add_user.php" class="btn btn-primary" style="padding: 12px 24px;">+ Add New User</a>
        </header>

        <div class="card">
            <?php if (isset($_GET['msg'])): ?>
                <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: 15px; font-weight: 500;">
                    User account successfully <?php echo htmlspecialchars($_GET['msg']); ?>!
                </div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>System Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 500; color: var(--text-dark);"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span style="font-weight: 600; font-size: 0.9em; text-transform: uppercase; color: <?php echo $row['role'] === 'admin' ? 'purple' : ($row['role'] === 'organizer' ? 'blue' : 'green'); ?>;">
                                        <?php echo htmlspecialchars($row['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['is_verified'] == 1): ?>
                                        <span style="color: var(--success); font-weight: bold;">Approved</span>
                                    <?php else: ?>
                                        <span style="color: var(--warning); font-weight: bold;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn" style="background: var(--secondary); color: black; padding: 6px 12px; font-size: 0.85rem;">Edit</a>
                                        <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn trigger-modal" data-action="delete" style="background: var(--danger); color: white; padding: 6px 12px; font-size: 0.85rem;">Delete</a>
                                        <?php if ($row['is_verified'] == 0): ?>
                                            <a href="process_user.php?id=<?php echo $row['id']; ?>&action=approve" class="btn trigger-modal" data-action="approve" style="background: var(--success); color: white; padding: 6px 12px; font-size: 0.85rem;">Approve</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px 0; color: var(--text-muted);">No other users are currently registered in the database.</p>
            <?php endif; ?>
        </div>
    </main>

</div>

<?php include '../../includes/ui-components/action_modal.php'; ?>
</body>
</html>