<?php
// Database connection
$mysqli = new mysqli("db5017823017.hosting-data.io", "dbu1440831", "slowturtle@2025", "dbs14215974");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// List of topics with wildcard support for RSS feeds
$topics = [
    'bitcoin' => [
        'https://rss.feedspot.com/bitcoin_rss_feeds/',
        'https://rss.feedspot.com/bitcoin_news_rss_feeds/',
        'https://cointelegraph.com/rss-feeds'
    ],
    'bitcoin_ira' => [
        'https://rss.feedspot.com/ira_rss_feeds/',
        'https://blockchain.news/feed'
    ],
    'trump' => [
        'https://www.axios.com/2025/05/07/trump-crypto-stablecoin-bill',
        'https://www.investopedia.com/what-s-inside-and-why-it-matters-11718655',
        'https://rss.feedspot.com/donald_trump_rss_feeds/'
    ],
    'politics' => [
        'https://rss.feedspot.com/cryptocurrency_rss_feeds/',
        'https://coincodecap.com/crypto-rss-feeds'
    ],
    'bitcoin_prices' => [
        'https://coinjournal.net/feeds/',
        'http://www.rsscrypto.com/'
    ],
    'digital_gold' => [
        'http://www.rsscrypto.com/',
        'https://rss.feedspot.com/cryptocurrency_rss_feeds/'
    ]
];

// Function to fetch and save RSS feed data
function fetchAndSaveRSS($mysqli, $url, $topic) {
    $rss = @simplexml_load_file($url);
    if (!$rss) {
        echo "Error loading RSS feed: $url\n";
        return;
    }

    foreach ($rss->channel->item as $item) {
        $title = $mysqli->real_escape_string($item->title);
        $link = $mysqli->real_escape_string($item->link);
        $summary = isset($item->description) ? $mysqli->real_escape_string(strip_tags($item->description)) : '';
        $publishedDate = date('Y-m-d H:i:s', strtotime($item->pubDate));
        $source = parse_url($url, PHP_URL_HOST);

        $query = "INSERT INTO your_existing_table (title, link, summary, published_date, source, topic)
                  VALUES ('$title', '$link', '$summary', '$publishedDate', '$source', '$topic')
                  ON DUPLICATE KEY UPDATE summary='$summary', published_date='$publishedDate'";
        if (!$mysqli->query($query)) {
            echo "Error inserting data: " . $mysqli->error . "\n";
        }
    }
}

foreach ($topics as $topic => $urls) {
    foreach ($urls as $url) {
        fetchAndSaveRSS($mysqli, $url, $topic);
    }
}

echo "All RSS feeds fetched and stored successfully.";

$mysqli->close();
?>