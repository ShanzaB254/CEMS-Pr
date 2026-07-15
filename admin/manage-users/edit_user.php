<?php
// admin/manage-users/edit.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

$message = '';
$user_data = null;

if (isset($_GET['id'])) {
    $target_id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM users WHERE id = $target_id");
    
    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
    } else {
        die("User account not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $target_id = intval($_POST['user_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $role = $conn->real_escape_string($_POST['role']);
    $new_password = trim($_POST['password']);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET name='$name', email='$email', role='$role', password='$hashed_password' WHERE id=$target_id";
    } else {
        $update_sql = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$target_id";
    }
    
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
    <title>Edit User - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>CEMS Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-item">Dashboard</a>
            <a href="index.php" class="nav-item active">Manage Users</a>
            <a href="../announcements.php" class="nav-item">Announcements</a>
            <a href="../profile.php" class="nav-item">My Profile</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Modify User Profile</h1>
            <p>Update system parameters, assign permissions, or reset credentials.</p>
        </header>

        <div class="card" style="max-width: 600px;">
            <?php echo $message; ?>
            
            <?php if ($user_data): ?>
            <form action="edit.php?id=<?php echo $user_data['id']; ?>" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Access Role</label>
                    <select name="role" required>
                        <option value="user" <?php if($user_data['role'] == 'user') echo 'selected'; ?>>Student / Staff</option>
                        <option value="organizer" <?php if($user_data['role'] == 'organizer') echo 'selected'; ?>>Event Organizer</option>
                        <option value="admin" <?php if($user_data['role'] == 'admin') echo 'selected'; ?>>Administrator</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <label>Reset Password <span style="font-weight: normal; color: var(--text-muted); font-size: 0.85rem;">(Leave blank to keep existing password)</span></label>
                    <input type="password" name="password" placeholder="Enter new password">
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="update_user" class="btn btn-primary" style="flex: 2;">Save Changes</button>
                    <a href="../index.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>