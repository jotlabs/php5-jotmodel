

--
-- Books
--
CREATE TABLE IF NOT EXISTS `books` (
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,

    title       VARCHAR(255),
    subtitle    VARCHAR(255),
    slug        VARCHAR(255) UNIQUE,

    isbn        VARCHAR(16),
    publishDate DATETIME
);


--
-- Authors
--
CREATE TABLE IF NOT EXISTS `authors` (
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,

    name        VARCHAR(255),
    slug        VARCHAR(255) UNIQUE
);


--
-- Publishers
--
CREATE TABLE IF NOT EXISTS `publishers` (
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,

    name        VARCHAR(255),
    slug        VARCHAR(255) UNIQUE
);


--
--
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

