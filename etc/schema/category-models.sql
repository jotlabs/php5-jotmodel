--
-- Model: Categories
-- Decorates: ContentEnvelope
--

CREATE TABLE IF NOT EXISTS `category_collections` (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31),
    weight          INTEGER
);


CREATE TABLE IF NOT EXISTS `categories` (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
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


