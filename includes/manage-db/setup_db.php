<?php
// includes/setup_db.php
require_once 'db_connect.php';

echo "<h2>Starting Database Initialization...</h2>";

// 1. Users Table
// Handles Admin, Organizer, and Registered User roles[cite: 9, 10, 16, 22].
$table_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'organizer', 'user') NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($table_users) === TRUE) {
    echo "<p>✅ Users table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Users table: " . $conn->error . "</p>";
}

// 2. Events Table`
// Stores event details and requires Admin approval[cite: 13, 18, 30].
$table_events = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($table_events) === TRUE) {
    echo "<p>✅ Events table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Events table: " . $conn->error . "</p>";
}

// 3. Registrations Table
// Tracks both standard attendees and volunteers[cite: 25, 26].
$table_registrations = "CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('attendee', 'volunteer') NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($table_registrations) === TRUE) {
    echo "<p>✅ Registrations table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Registrations table: " . $conn->error . "</p>";
}

// 4. Feedback Table
// Stores ratings and feedback from attendees after an event concludes[cite: 21, 28].
$table_feedback = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($table_feedback) === TRUE) {
    echo "<p>✅ Feedback table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Feedback table: " . $conn->error . "</p>";
}
// 5. Notifications Table
// Stores in-app messages sent from organizers to registered attendees
$table_notifications = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    event_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)";

if ($conn->query($table_notifications) === TRUE) {
    echo "<p>✅ Notifications table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Notifications table: " . $conn->error . "</p>";
}
// 6. Announcements Table
// Stores site-wide broadcasts managed by the Admin
$table_announcements = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($table_announcements) === TRUE) {
    echo "<p>✅ Announcements table created successfully.</p>";
} else {
    echo "<p>❌ Error creating Announcements table: " . $conn->error . "</p>";
}
$conn->close();
echo "<h3>Database Initialization Complete.</h3>";
?>