CREATE TABLE posts (
    id              INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    publish_date    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title           VARCHAR(200) NOT NULL,
    content         TEXT NOT NULL
);
