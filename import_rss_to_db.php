<?php
/**
 * Import RSS Feed into an Existing Table
 * Fetches articles from the Bitcoin Magazine RSS feed and inserts them into your MySQL table.
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host_name = 'db5017823017.hosting-data.io';
$database = 'dbs14215974';
$user_name = 'dbu1440831';
$password = 'slowturtle@2025';

// Create a new MySQLi connection
$link = new mysqli($host_name, $user_name, $password, $database);

// Check for connection errors
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
}

// RSS Feed URL
$rss_feed_url = "http://bitcoinmagazine.com/feed";

// Fetch the RSS feed
$rss_content = @file_get_contents($rss_feed_url);
if ($rss_content === FALSE) {
    die('<p>Failed to fetch the RSS feed. Please check the feed URL or your server\'s internet connection.</p>');
}

// Parse the RSS feed into a SimpleXML object
$rss = @simplexml_load_string($rss_content);
if ($rss === FALSE) {
    die('<p>Failed to parse the RSS feed. Please check the feed format.</p>');
}

// SQL query to insert data (with duplicate prevention)
$stmt = $link->prepare("
    INSERT INTO your_existing_table (title, link, summary, published_date) 
    VALUES (?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE 
        summary = VALUES(summary), 
        published_date = VALUES(published_date)
");
if (!$stmt) {
    die('<p>Failed to prepare the SQL statement: ' . $link->error . '</p>');
}

// Loop through each item in the RSS feed and insert it into the database
foreach ($rss->channel->item as $item) {
    $title = $item->title;
    $link = $item->link;
    $summary = $item->description;
    $published_date = date('Y-m-d H:i:s', strtotime($item->pubDate)); // Convert pubDate to MySQL DATETIME format

    // Bind parameters and execute the statement
    $stmt->bind_param('ssss', $title, $link, $summary, $published_date);
    if (!$stmt->execute()) {
        echo '<p>Failed to import: ' . htmlspecialchars($title) . ' - Error: ' . $stmt->error . '</p>';
    }
}

// Close the prepared statement and database connection
$stmt->close();
$link->close();
?>