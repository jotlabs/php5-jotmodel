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
    id      INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63) UNIQUE
);



--
-- Content Models -- classifies content into Models known by the Application
--
CREATE TABLE IF NOT EXISTS `content_models` (
    id      INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63) UNIQUE
);


--
-- Content Types -- maps the content envelope model to the table schema
--                  type is a sub-division of models. (Many types of article model)
--
CREATE TABLE IF NOT EXISTS `content_types` (
    id          INTEGER PRIMARY KEY AUTO_INCREMENT,
    modelId     INTEGER,
    slug        VARCHAR(63) UNIQUE,
    title       VARCHAR(63),

    FOREIGN KEY (`modelId`) REFERENCES `content_models`(`id`)
);


--
-- Content Envelope: generic data for custom content types
--
CREATE TABLE IF NOT EXISTS `content` (
    envelopeId      INTEGER PRIMARY KEY AUTO_INCREMENT,
    statusId        INTEGER,

    typeId          INTEGER,
    contentId       INTEGER,

    slug            VARCHAR(255),
    title           VARCHAR(255),
    excerpt         TEXT,

    extra1          VARCHAR(255),
    extra2          VARCHAR(255),

    pageUrl         VARCHAR(255) UNIQUE,
    permalink       VARCHAR(255) UNIQUE,
    imageTemplate   VARCHAR(255),

    dateAdded       DATETIME,
    dateUpdated     TIMESTAMP,

    guid            VARCHAR(255) UNIQUE,
    version         INTEGER,
    score           INTEGER,

    FOREIGN KEY (`typeId`)   REFERENCES `content_types`(`id`),
    FOREIGN KEY (`statusId`) REFERENCES `content_status`(`id`)
);

CREATE INDEX `content_1` ON `content` ( statusId, slug );
CREATE INDEX `content_2` ON `content` ( typeId, contentId );
CREATE INDEX `content_3` ON `content` ( slug );

--
-- View Model: ContentEnvelope
-- A one-table view of the four relational content envelope tables
-- TODO: make into a pseudo-materialised view.
--
CREATE VIEW `content_envelope` AS
SELECT
    content.envelopeId    AS envelopeId,
    content.contentId     AS contentId,
    content.statusId      AS statusId,
    content_models.id     AS modelId,
    content.typeId        AS typeId,
    content_status.slug   AS status,
    content_models.slug   AS model,
    content_types.slug    AS type,
    content.slug          AS slug,
    content.title         AS title,
    content.excerpt       AS excerpt,
    content.extra1        AS extra1,
    content.extra2        AS extra2,
    content.pageUrl       AS pageUrl,
    content.permalink     AS permalink,
    content.imageTemplate AS imageTemplate,
    content.dateAdded     AS dateAdded,
    content.dateUpdated   AS dateUpdated,
    content.guid          AS guid,
    content.version       AS version,
    content.score         AS score
FROM `content`
LEFT JOIN `content_status` ON content.statusId = content_status.id
LEFT JOIN `content_types`  ON content.typeId = content_types.id
LEFT JOIN `content_models` ON content_types.modelId  = content_models.id;


--
-- View Model: Content Type Models
-- A one-table view of content model and it's associated content type
--
CREATE VIEW `content_type_models` AS
SELECT
    models.id       AS id,
    models.slug     AS slug,
    models.title    AS title,
    types.id        AS typeId,
    types.slug      AS typeSlug,
    types.title     AS typeTitle
FROM `content_types` AS `types`
LEFT JOIN `content_models` AS `models` ON types.modelId = models.id;

