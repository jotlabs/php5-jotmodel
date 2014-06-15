--
-- Authors
--
CREATE TABLE IF NOT EXISTS `authors` (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,

    name        VARCHAR(255),
    slug        VARCHAR(255) UNIQUE
);


--
-- Publishers
--
CREATE TABLE IF NOT EXISTS `publishers` (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,

    name        VARCHAR(255),
    slug        VARCHAR(255) UNIQUE
);


--
-- Books
--
CREATE TABLE IF NOT EXISTS `books` (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,

    title       VARCHAR(255),
    subtitle    VARCHAR(255),
    slug        VARCHAR(255) UNIQUE,

    isbn        VARCHAR(16),
    publishDate DATETIME
);


--
-- Book Authors/Publishers mapping tables
--
CREATE TABLE IF NOT EXISTS `book_authors` (
    bookId      INTEGER,
    authorId    INTEGER,

    FOREIGN KEY (`bookId`)   REFERENCES `books`(`id`),
    FOREIGN KEY (`authorId`) REFERENCES `authors`(`id`)
);


CREATE TABLE IF NOT EXISTS `book_publishers` (
    bookId      INTEGER,
    publisherId INTEGER,

    FOREIGN KEY (`bookId`)      REFERENCES `books`(`id`),
    FOREIGN KEY (`publisherId`) REFERENCES `publishers`(`id`)
);

