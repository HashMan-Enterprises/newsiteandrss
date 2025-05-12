<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Increase execution time and memory limit
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// Database connection
$host_name = 'db5017823017.hosting-data.io';
$database = 'dbs14215974';
$user_name = 'dbu1440831';
$password = 'Smokin@2025';

$link = new mysqli($host_name, $user_name, $password, $database);
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
} else {
    echo '<p>Connected to the database successfully!</p>';
}

// Fetch and parse RSS feed
$rss_feed_url = "http://bitcoinmagazine.com/feed";
$rss_content = @file_get_contents($rss_feed_url);
if ($rss_content === FALSE) {
    die('<p>Failed to fetch the RSS feed. Check the URL or server connection.</p>');
}

$rss = @simplexml_load_string($rss_content);
if ($rss === FALSE) {
    die('<p>Failed to parse the RSS feed. Check the feed format.</p>');
}

echo '<p>RSS feed fetched and parsed successfully!</p>';

// Close database connection
$link->close();
?>