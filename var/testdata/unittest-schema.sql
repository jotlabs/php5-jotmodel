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
-- Content Models -- classifies content into Models known by the Application
--
CREATE TABLE IF NOT EXISTS `content_models` (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    slug    VARCHAR(63) UNIQUE,
    title   VARCHAR(63) UNIQUE
);


--
-- Content Types -- maps the content envelope model to the table schema
--                  type is a sub-division of models. (Many types of article model)
--
CREATE TABLE IF NOT EXISTS `content_types` (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    modelId     INTEGER,
    slug        VARCHAR(63) UNIQUE,
    title       VARCHAR(63),

    FOREIGN KEY (`modelId`) REFERENCES `content_models`(`id`)
);


--
-- Content Envelope: generic data for custom content types
--
CREATE TABLE IF NOT EXISTS `content` (
    envelopeId      INTEGER PRIMARY KEY AUTOINCREMENT,
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
-- TODO: Replace with a pseudo-materialised view
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


--
-- Model: Tags
-- Decorates: ContentEnvelope
--

CREATE TABLE IF NOT EXISTS `tag_collections` (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31)
);


CREATE TABLE IF NOT EXISTS `tags` (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    collectionId    INTEGER,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31),

    FOREIGN KEY (`collectionId`) REFERENCES `tag_collections`(`id`)
);


CREATE TABLE IF NOT EXISTS `content_tags` (
    contentId   INTEGER,
    tagId       INTEGER,
    dateAdded   DATETIME,

    FOREIGN KEY (`contentId`) REFERENCES `content`(`envelopeId`),
    FOREIGN KEY (`tagId`)     REFERENCES `tags`(`id`)
);

CREATE INDEX `content_tags_1` ON `content_tags` (`contentId`, `tagId`);
CREATE INDEX `content_tags_2` ON `content_tags` (`tagId`, `contentId`);

--
-- VIEW Model: Tag
--
CREATE VIEW `tagged_content` AS
SELECT
    content_tags.contentId AS envelopeId,
    content_tags.tagId     AS tagId,
    tags.slug              AS tag,
    tags.name              AS name,
    tags.collectionId      AS collectionId,
    tag_collections.slug   AS collectionSlug,
    tag_collections.name   AS collectionName,
    content_tags.dateAdded AS dateAdded
FROM `content_tags`
LEFT JOIN `tags` ON `content_tags`.tagId = `tags`.id
LEFT JOIN `tag_collections` ON `tags`.collectionId = `tag_collections`.id;



--
-- Model: Categories
-- Decorates: ContentEnvelope
--

CREATE TABLE IF NOT EXISTS `category_collections` (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31),
    weight          INTEGER
);


CREATE TABLE IF NOT EXISTS `categories` (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    collectionId    INTEGER,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31),

    FOREIGN KEY (`collectionId`) REFERENCES `category_collections`(`id`)
);


CREATE TABLE IF NOT EXISTS `content_categories` (
    contentId   INTEGER,
    categoryId  INTEGER,
    dateAdded   DATETIME,

    FOREIGN KEY (`contentId`)  REFERENCES `content`(`envelopeId`),
    FOREIGN KEY (`categoryId`) REFERENCES `categories`(`id`)
);

CREATE INDEX `content_categories_1` ON `content_categories` (`contentId`, `categoryId`);
CREATE INDEX `content_categories_2` ON `content_categories` (`categoryId`, `contentId`);


--
-- VIEW Model: Category
--
CREATE VIEW `category_content` AS
SELECT
    content_categories.contentId    AS envelopeId,
    content_categories.categoryId   AS categoryId,
    categories.slug                 AS category,
    categories.name                 AS name,
    categories.collectionId         AS collectionId,
    category_collections.slug       AS collectionSlug,
    category_collections.name       AS collectionName,
    category_collections.weight     AS collectionWeight,
    content_categories.dateAdded    AS dateAdded
FROM `content_categories`
LEFT JOIN `categories` ON `content_categories`.categoryId = `categories`.id
LEFT JOIN `category_collections` ON `categories`.collectionId = `category_collections`.id;



