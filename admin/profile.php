<?php
// admin/profile.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

require_once '../includes/manage-db/db_connect.php';

$message = '';
$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $new_password = trim($_POST['password']);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET name='$name', email='$email', password='$hashed_password' WHERE id=$admin_id";
    } else {
        $update_sql = "UPDATE users SET name='$name', email='$email' WHERE id=$admin_id";
    }
    
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['name'] = $name; 
        $message = "<div style='background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;'>Profile details saved successfully!</div>";
    } else {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Database Exception: " . $conn->error . "</div>";
    }
}

$result = $conn->query("SELECT name, email FROM users WHERE id = $admin_id");
$admin_data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - CEMS</title>
    <link rel="stylesheet" href="../assets/css/common.css?v=<?php echo filemtime('../assets/css/common.css'); ?>">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo filemtime('../assets/css/admin.css'); ?>"> 
    <!-- <link rel="stylesheet" href="../assets/css/admin.css"> -->
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
            <a href="announcements.php" class="nav-item">Announcements</a>
            <a href="profile.php" class="nav-item active">My Profile</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../public/logout.php" class="nav-item nav-item-danger">Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Administrative Credentials</h1>
            <p>Update personal administrative access credentials and security metrics.</p>
        </header>

        <div class="card" style="max-width: 600px;">
            <?php echo $message; ?>
            
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label>Administrator Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($admin_data['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                </div>

                <div class="form-group" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <label>Modify Password <span style="font-weight: normal; color: var(--text-muted); font-size: 0.85rem;">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="Enter highly secure password">
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary" style="margin-top: 15px; width: 100%;">Save Changes</button>
            </form>
        </div>
    </main>

</div>

</body>
</html>