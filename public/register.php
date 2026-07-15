<?php
// public/register.php
require_once '../includes/manage-db/db_connect.php';

$message = ''; // Variable to hold success/error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Sanitize user inputs to prevent basic SQL injection
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    if (empty($name) || empty($email) || empty($password)) {
        $message = "<p style='color: red; text-align: center;'>All fields are required.</p>";
    } else {
        // Check if the email is already registered
        $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($check_email->num_rows > 0) {
            $message = "<p style='color: red; text-align: center;'>This university email is already registered.</p>";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "<p style='color: green; text-align: center; font-weight: bold;'>Registration received! <br>Your account is pending Admin approval. You will be able to login once verified.</p>";
            } else {
                $message = "<p style='color: red; text-align: center;'>Database Error: " . $conn->error . "</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CEMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create an Account</h2>
        
        <?php echo $message; ?> 
        
        <form action="register.php" method="POST">
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">University Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">I am registering as:</label>
                <select id="role" name="role" required>
                    <option value="user">Student / Staff</option>
                    <option value="organizer">Event Organizer</option>
                </select>
            </div>

            <button type="submit" name="register" class="btn">Register</button>
            
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </div>
</div>

</body>
</html>