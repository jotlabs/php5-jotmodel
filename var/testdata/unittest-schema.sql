--
-- Model: Content
-- Dependencies: none
--
-- Core Content Envelope schema
--

--
-- Content Status -- an enumeration of States content can be in
--
CREATE TABLE IF NOT EXISTS `content_status` (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63) UNIQUE
);



--
-- Content Types -- maps the content envelope model to the table schema
--
CREATE TABLE IF NOT EXISTS `content_types` (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63)
);


--
-- Content Models -- classifies content into Models known by the Application
--
CREATE TABLE IF NOT EXISTS `content_models` (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    typeId  INTEGER,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63) UNIQUE,

    FOREIGN KEY (`typeId`) REFERENCES `content_types`(`id`)
);


--
-- Content Envelope: generic data for custom content types
--
CREATE TABLE IF NOT EXISTS `content` (
    envelopeId      INTEGER PRIMARY KEY AUTOINCREMENT,
    statusId        INTEGER,

    modelId         INTEGER,
    contentId       INTEGER,

    slug            VARCHAR(255),
    title           VARCHAR(255),
    excerpt         TEXT,

    permalink       VARCHAR(255) UNIQUE,
    imageTemplate   VARCHAR(255),

    dateAdded       DATETIME,
    dateUpdated     TIMESTAMP,

    FOREIGN KEY (`modelId`)  REFERENCES `content_models`(`id`),
    FOREIGN KEY (`statusId`) REFERENCES `content_status`(`id`)
);

CREATE INDEX `content_1` ON `content` ( statusId, slug );
CREATE INDEX `content_2` ON `content` ( modelId, contentId );
CREATE INDEX `content_3` ON `content` ( slug );

--
-- A one-table view of the four relational content envelope tables
-- TODO: Replace with a pseudo-materialised view
--
CREATE VIEW `content_envelope` AS
SELECT
    content.envelopeId    AS envelopeId,
    content.contentId     AS contentId,
    content.statusId      AS statusId,
    content.modelId       AS modelId,
    content_types.id      AS typesId,
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
LEFT JOIN `content_status` ON content.statusId = content_status.id
LEFT JOIN `content_models` ON content.modelId  = content_models.id
LEFT JOIN `content_types`  ON content_models.typeId = content_types.id;


--
-- Content: Videos (from YouTube)
--
CREATE TABLE IF NOT EXISTS `videos` (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,

    sourceId        VARCHAR(63) UNIQUE,
    sourceUrl       VARCHAR(255) UNIQUE,

    posterName      VARCHAR(255),
    posterProfile   VARCHAR(255),
    datePosted      DATETIME,

    duration        INTEGER,
    numberViews     INTEGER
);
