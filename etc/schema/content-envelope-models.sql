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
    _id             INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63) UNIQUE
);



--
-- Content Types -- maps the content envelope model to the table schema
--
CREATE TABLE IF NOT EXISTS `content_types` (
    _id             INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63)
);


--
-- Content Models -- classifies content into Models known by the Application
--
CREATE TABLE IF NOT EXISTS `content_models` (
    _id             INTEGER PRIMARY KEY AUTO_INCREMENT,
    _type_id        INTEGER,
    slug            VARCHAR(63) UNIQUE,
    title           VARCHAR(63) UNIQUE,

    FOREIGN KEY (`_type_id`) REFERENCES `content_types`(`_id`)
);


--
-- Content Envelope: generic data for custom content types
--
CREATE TABLE IF NOT EXISTS `content` (
    _id             INTEGER PRIMARY KEY AUTO_INCREMENT,
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

--
-- A one-table view of the four relational content envelope tables
--
CREATE VIEW `content_envelope` AS
SELECT
    content._id           AS _envelope_id,
    content._content_id   AS _content_id,
    content._status_id    AS _status_id,
    content._model_id     AS _model_id,
    content_types._id     AS _types_id,
    content_status.slug   AS status,
    content_models.slug   AS model,
    content_types.slug    AS type,
    content.slug          AS slug,
    content.title         AS title,
    content.excerpt       AS excerpt,
    content.permalink     AS permalink,
    content.imageTemplate AS imageTemplate,
    content.dateAdded     AS dateAdded,
    content.dateUpdated   AS dateUpdated
FROM `content`
LEFT JOIN `content_status` ON content._status_id = content_status._id
LEFT JOIN `content_models` ON content._model_id = content_models._id
LEFT JOIN `content_types`  ON content_models._type_id = content_types._id;
