<?php
/**
 * One-Time RSS Feed Import Script with Fixes
 * Handles large datasets and prevents "MySQL server has gone away" errors.
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

// Increase PHP timeout
set_time_limit(300);

// Create a new MySQLi connection with persistent connection
$link = new mysqli('p:' . $host_name, $user_name, $password, $database);

// Check for connection errors
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
}

// RSS Feed URL (Using NASA Breaking News as an example)
$rss_feed_url = "https://www.nasa.gov/rss/dyn/breaking_news.rss";

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

// Batch insert data into the database
echo '<h2>Inserting Data into Database</h2>';
$insert_values = [];
foreach ($rss->channel->item as $item) {
    $title = $link->real_escape_string($item->title);
    $link_url = $link->real_escape_string($item->link);
    $summary = $link->real_escape_string($item->description);
    $published_date = date('Y-m-d H:i:s', strtotime($item->pubDate));
    $insert_values[] = "('$title', '$link_url', '$summary', '$published_date')";
}

// Insert all rows at once
if (!empty($insert_values)) {
    $query = "
        INSERT INTO your_existing_table (title, link, summary, published_date) 
        VALUES " . implode(',', $insert_values) . "
        ON DUPLICATE KEY UPDATE 
            summary = VALUES(summary), 
            published_date = VALUES(published_date)";
    if (!$link->query($query)) {
        die('<p>Failed to insert data: ' . $link->error . '</p>');
    } else {
        echo '<p>Data inserted successfully!</p>';
    }
} else {
    echo '<p>No data to insert.</p>';
}

// Close the database connection
$link->close();

echo '<p>All done! Check your database for the imported data.</p>';
?>