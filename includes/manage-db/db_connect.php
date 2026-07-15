<?php

$host = 'localhost';
$db_name = 'cems_db';
$username = 'root'; // Default username for local XAMPP
$password = '';     // Default password for local XAMPP is usually empty

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
} 
// echo "Connected successfully!"; // Uncomment this later to test!
?>