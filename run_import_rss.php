<?php
/**
 * Run RSS Feed Import Script
 * Fetches articles from the Bitcoin Magazine RSS feed and inserts them into your MySQL table.
 * Displays the imported data for immediate verification.
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection script
require_once 'db_connection.php'; // Ensure this file is in the same directory or update the path

// RSS Feed URL
$rss_feed_url = "http://bitcoinmagazine.com/feed";

// Fetch the RSS feed
$rss_content = file_get_contents($rss_feed_url);
if ($rss_content === FALSE) {
    die('<p>Failed to fetch the RSS feed. Please check the URL or your server\'s internet connection.</p>');
}

// Parse the RSS feed into a SimpleXML object
$rss = simplexml_load_string($rss_content);
if ($rss === FALSE) {
    die('<p>Failed to parse the RSS feed. Please check the feed format.</p>');
}

// Prepare SQL query to insert data (with duplicate prevention)
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
echo '<h3>Importing RSS Feed...</h3>';
foreach ($rss->channel->item as $item) {
    $title = $item->title;
    $link = $item->link;
    $summary = $item->description;
    $published_date = date('Y-m-d H:i:s', strtotime($item->pubDate)); // Convert pubDate to MySQL DATETIME format

    // Bind parameters and execute the statement
    $stmt->bind_param('ssss', $title, $link, $summary, $published_date);
    if ($stmt->execute()) {
        echo '<p>Imported: ' . htmlspecialchars($title) . '</p>';
    } else {
        echo '<p>Failed to import: ' . htmlspecialchars($title) . ' - Error: ' . $stmt->error . '</p>';
    }
}

// Close the prepared statement and database connection
$stmt->close();
$link->close();

echo '<h3>RSS feed imported successfully!</h3>';
?>