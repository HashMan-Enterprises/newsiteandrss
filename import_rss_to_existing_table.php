<?php
/**
 * Import RSS Feed into an Existing Table
 * Fetches articles from the Bitcoin Magazine RSS feed and inserts them into your existing MySQL table.
 */

// Include the database connection script
require_once 'db_connection.php'; // Ensure this file is in the same directory or update the path

// RSS Feed URL
$rss_feed_url = "http://bitcoinmagazine.com/feed";

// Fetch the RSS feed
$rss_content = file_get_contents($rss_feed_url);
if ($rss_content === FALSE) {
    error_log("Failed to fetch the RSS feed from $rss_feed_url");
    die('<p>Unable to fetch the RSS feed. Please try again later.</p>');
}

// Parse the RSS feed into a SimpleXML object
$rss = simplexml_load_string($rss_content);
if ($rss === FALSE) {
    error_log("Failed to parse the RSS feed.");
    die('<p>Invalid RSS feed format.</p>');
}

// SQL query to insert data (with duplicate prevention)
$stmt = $link->prepare("INSERT INTO your_existing_table (title, link, summary, published_date) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), summary = VALUES(summary), published_date = VALUES(published_date)");
if (!$stmt) {
    error_log("Failed to prepare the SQL statement: " . $link->error);
    die('<p>We are currently experiencing technical difficulties. Please try again later.</p>');
}

// Loop through each item in the RSS feed
foreach ($rss->channel->item as $item) {
    $title = $item->title;
    $link = $item->link;
    $summary = $item->description;
    $published_date = date('Y-m-d H:i:s', strtotime($item->pubDate)); // Convert pubDate to MySQL DATETIME format

    // Bind parameters and execute the statement
    $stmt->bind_param('ssss', $title, $link, $summary, $published_date);
    if (!$stmt->execute()) {
        error_log("Failed to execute SQL statement: " . $stmt->error);
    }
}

// Close the prepared statement and database connection
$stmt->close();
$link->close();

echo '<p>RSS feed imported successfully into your existing table!</p>';
?>