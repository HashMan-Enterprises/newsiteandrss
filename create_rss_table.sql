CREATE TABLE rss_feed_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    link TEXT NOT NULL,
    summary TEXT,
    published_date DATETIME,
    source VARCHAR(255),
    topic VARCHAR(50),
    UNIQUE(link) -- Prevent duplicate entries
);