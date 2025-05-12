<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host_name = 'db5017823017.hosting-data.io';
$database = 'dbs14215974';
$user_name = 'dbu1440831';
$password = 'Smokin@2025'; // Update this if you reset the password

// Create a new MySQLi connection
$link = new mysqli($host_name, $user_name, $password, $database);

// Check for connection errors
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
} else {
    echo '<p>Connection to MySQL server successfully established.</p>';
}

// Close the connection
$link->close();
?>