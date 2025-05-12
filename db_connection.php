<?php
/**
 * Database Connection Script
 */

// Database credentials
$host_name = 'db5017823017.hosting-data.io';
$database = 'dbs14215974';
$user_name = 'dbu1440831';
$password = 'Smokin@2025';

// Create a new MySQLi connection
$link = new mysqli($host_name, $user_name, $password, $database);

// Check for connection errors
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
} else {
    echo '<p>Connection to MySQL server successfully established.</p>';
}

// Set character set to UTF-8
if (!$link->set_charset("utf8")) {
    die('<p>Failed to set character set to UTF-8.</p>');
}
?>