<?php
// Database connection
$mysqli = new mysqli("db5017823017.hosting-data.io", "dbu1440831", "slowturtle@2025", "dbs14215974");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Query to fetch RSS feed data
$query = "SELECT title, link, summary, published_date, source, topic FROM rss_feed_data ORDER BY published_date DESC";
$result = $mysqli->query($query);

// Check if there are any results
if ($result->num_rows > 0) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RSS Feed Data</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f4f4f4; }
            a { color: #007BFF; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <h1>RSS Feed Data</h1>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Summary</th>
                    <th>Published Date</th>
                    <th>Source</th>
                    <th>Topic</th>
                </tr>
            </thead>
            <tbody>';
    
    // Output each row as a table row
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
            <td><a href="' . htmlspecialchars($row['link']) . '" target="_blank">' . htmlspecialchars($row['title']) . '</a></td>
            <td>' . htmlspecialchars($row['summary']) . '</td>
            <td>' . htmlspecialchars($row['published_date']) . '</td>
            <td>' . htmlspecialchars($row['source']) . '</td>
            <td>' . htmlspecialchars($row['topic']) . '</td>
        </tr>';
    }

    echo '</tbody>
        </table>
    </body>
    </html>';
} else {
    echo "No RSS feed data found.";
}

// Close database connection
$mysqli->close();
?>