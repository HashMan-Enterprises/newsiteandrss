<?php
/**
 * Import RSS Feed using cURL
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

// RSS Feed URL
$rss_feed_url = "http://bitcoinmagazine.com/feed";

// Fetch RSS feed using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $rss_feed_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$rss_content = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || !$rss_content) {
    die('<p>Failed to fetch the RSS feed. HTTP Code: ' . $http_code . '</p>');
}

// Parse the RSS feed into a SimpleXML object
$rss = simplexml_load_string($rss_content);
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