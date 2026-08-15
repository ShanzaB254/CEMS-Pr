<?php
// admin/manage-users/add_user.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    if (empty($name) || empty($email) || empty($password)) {
        $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>All fields are required.</div>";
    } else {
        $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($check_email->num_rows > 0) {
            $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>This email is already registered.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Manually provisioned accounts bypass the verification gate automatically
            $sql = "INSERT INTO users (name, email, password, role, is_verified) VALUES ('$name', '$email', '$hashed_password', '$role', 1)";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: manage_users.php?msg=added");
                exit();
            } else {
                $message = "<div style='background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Database Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provision User - CEMS</title>
    <link rel="stylesheet" href="../../assets/css/common.css?v=<?php echo filemtime('../../assets/css/common.css'); ?>">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo filemtime('../../assets/css/admin.css'); ?>"> 
    <!-- <link rel="stylesheet" href="../../assets/css/admin.css"> -->
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
        <header style="margin-bottom: 30px;">
            <h1>Provision New User</h1>
            <p>Directly register a verified student, organizer, or admin account.</p>
        </header>

        <div class="card" style="max-width: 600px;">
            <?php echo $message; ?>
            
            <form action="add_user.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="e.g., Jane Doe">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="e.g., user@university.edu">
                </div>

                <div class="form-group">
                    <label>Temporary Password</label>
                    <input type="text" name="password" required placeholder="e.g., TempPass123!">
                </div>

                <div class="form-group">
                    <label>Assign Role</label>
                    <select name="role" required>
                        <option value="user">Student / Staff</option>
                        <option value="organizer">Event Organizer</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="add_user" class="btn btn-primary" style="flex: 2;">Create Account</button>
                    <a href="../manage_users.php" class="btn" style="background: #e5e7eb; color: #374151; flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </main>

</div>

</body>
</html>