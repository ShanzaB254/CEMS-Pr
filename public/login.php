<?php
// public/login.php
session_start(); // Start the session to track logged-in users
require_once '../includes/manage-db/db_connect.php';

$message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "<p style='color: red; text-align: center;'>Please fill in all fields.</p>";
    } else {
     // Query the database for the user (Added is_verified to the SELECT list)
        $sql = "SELECT id, name, password, role, is_verified FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                
                // SECURITY GATE: Check if the account is approved by an Admin
                // (We automatically let Admins bypass this in case they lock themselves out)
                if ($user['is_verified'] == 1 || $user['role'] === 'admin') {
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] == 'admin') {
                        header("Location: ../admin/index.php");
                    } elseif ($user['role'] == 'organizer') {
                        header("Location: ../organizer/index.php");
                    } else {
                        header("Location: ../user/index.php");
                    }
                    exit(); 
                    
                } else {
                    $message = "<p style='color: #d97706; text-align: center; font-weight: bold;'>Account approval pending. Your account has not been approved by an administrator yet.</p>";
                }
                
            } else {
                $message = "<p style='color: red; text-align: center;'>Incorrect password.</p>";
            }
        } else {
            $message = "<p style='color: red; text-align: center;'>No account found with that email.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CEMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>System Login</h2>
        
        <?php echo $message; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">University Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="btn">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </div>
</div>

</body>
</html>