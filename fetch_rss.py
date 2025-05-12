import feedparser

# URL of the RSS feed
RSS_FEED_URL = "http://bitcoinmagazine.com/feed"

# Parsing the RSS feed
feed = feedparser.parse(RSS_FEED_URL)

# Display the feed title
print(f"Feed Title: {feed.feed.title}")

# Loop through the first 5 articles
for entry in feed.entries[:5]:
    print(f"Title: {entry.title}")
    print(f"Link: {entry.link}")
    print(f"Published: {entry.published}")
    print(f"Summary: {entry.summary}\n")