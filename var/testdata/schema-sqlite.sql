--
-- Model: Content
-- Dependencies: none
-- Decorates: none
-- Extends: none
--
-- Core Content Envelope schema
--

--
-- Content Status -- an enumeration of States content can be in
--
CREATE TABLE IF NOT EXISTS `content_status` (
    _id             INTEGER PRIMARY KEY AUTOINCREMENT,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63) UNIQUE
);



--
-- Content Types -- maps the content envelope to the table schema
--
CREATE TABLE IF NOT EXISTS `content_types` (
    _id             INTEGER PRIMARY KEY AUTOINCREMENT,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63)
);


--
-- Content Models -- classifies content into Models known by the Application
--
CREATE TABLE IF NOT EXISTS `content_models` (
    _id             INTEGER PRIMARY KEY AUTOINCREMENT,
    _type_id        INTEGER,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63) UNIQUE,

    FOREIGN KEY (`_type_id`) REFERENCES `content_types`(`_id`)
);


--
-- Content Envelope: generic data for custom content types
--
CREATE TABLE IF NOT EXISTS `content` (
    _id             INTEGER PRIMARY KEY AUTOINCREMENT,
    _status_id      INTEGER,

    _model_id       INTEGER,
    _content_id     INTEGER,

    slug            VARCHAR(255) UNIQUE,
    title           VARCHAR(255),
    excerpt         TEXT,

    permalink       VARCHAR(255),
    imageTemplate   VARCHAR(255),

    dateAdded       DATETIME,
    dateUpdated    TIMESTAMP,

    FOREIGN KEY (`_model_id`)  REFERENCES `content_models`(`_id`),
    FOREIGN KEY (`_status_id`) REFERENCES `content_status`(`_id`)
);

CREATE INDEX `content_1` ON `content` ( _status_id, slug );
CREATE INDEX `content_2` ON `content` ( _content_id );

