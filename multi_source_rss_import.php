<?php
/**
 * Multi-Source RSS Feed Importer
 * Fetches articles from various RSS feeds and inserts them into your MySQL table.
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
if ($link->connect_error) {
    die('<p>Failed to connect to MySQL: ' . $link->connect_error . '</p>');
}

// List of RSS feed URLs
$rss_feed_urls = [
    "https://www.coindesk.com/arc/outboundfeeds/rss/",  // Coindesk
    "https://cointelegraph.com/rss",                   // CoinTelegraph
    "https://cryptoslate.com/news/feed/",              // CryptoSlate
    "https://decrypt.co/feed"                          // Decrypt
];

// Prepare SQL query to insert data (with duplicate prevention)
$stmt = $link->prepare("
    INSERT INTO your_existing_table (title, link, summary, published_date, source) 
    VALUES (?, ?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE 
        summary = VALUES(summary), 
        published_date = VALUES(published_date)
");
if (!$stmt) {
    die('<p>Failed to prepare the SQL statement: ' . $link->error . '</p>');
}

echo '<h3>Importing RSS Feeds...</h3>';

foreach ($rss_feed_urls as $rss_feed_url) {
    echo '<h4>Fetching feed from: ' . htmlspecialchars($rss_feed_url) . '</h4>';

    // Fetch the RSS feed
    $rss_content = @file_get_contents($rss_feed_url);
    if ($rss_content === FALSE) {
        echo '<p>Failed to fetch the RSS feed from: ' . htmlspecialchars($rss_feed_url) . '</p>';
        continue; // Skip to the next feed
    }

    // Parse the RSS feed into a SimpleXML object
    $rss = @simplexml_load_string($rss_content);
    if ($rss === FALSE) {
        echo '<p>Failed to parse the RSS feed from: ' . htmlspecialchars($rss_feed_url) . '</p>';
        continue; // Skip to the next feed
    }

    // Loop through each item in the RSS feed and insert it into the database
    foreach ($rss->channel->item as $item) {
        $title = $item->title;
        $link = $item->link;
        $summary = $item->description;
        $published_date = date('Y-m-d H:i:s', strtotime($item->pubDate)); // Convert pubDate to MySQL DATETIME format

        // Extract the source domain (e.g., "coindesk.com")
        $source = parse_url($rss_feed_url, PHP_URL_HOST);

        // Bind parameters and execute the statement
        $stmt->bind_param('sssss', $title, $link, $summary, $published_date, $source);
        if ($stmt->execute()) {
            echo '<p>Imported: ' . htmlspecialchars($title) . '</p>';
        } else {
            echo '<p>Failed to import: ' . htmlspecialchars($title) . ' - Error: ' . $stmt->error . '</p>';
        }
    }
}

// Close the prepared statement and database connection
$stmt->close();
$link->close();

echo '<h3>All RSS feeds imported successfully!</h3>';
?>