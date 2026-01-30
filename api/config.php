<?php

// Database Configuration
$host = 'localhost';
$username = 'db_user';
$password = 'db_password';
db_name = 'db_name';

// Helper Functions
function connect_db() {
    global $host, $username, $password, $db_name;
    $conn = new mysqli($host, $username, $password, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function close_db($conn) {
    $conn->close();
}

?>