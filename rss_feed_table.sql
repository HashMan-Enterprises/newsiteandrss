-- Original table structure
CREATE TABLE your_existing_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    link TEXT NOT NULL,
    summary TEXT,
    published_date DATETIME,
    UNIQUE (link) -- Prevent duplicate entries based on link
);

-- Adding new columns for topic and source
ALTER TABLE your_existing_table
ADD COLUMN topic VARCHAR(50),
ADD COLUMN source VARCHAR(255);