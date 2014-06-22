--
-- Model: Tags
-- Decorates: ContentEnvelope
--

CREATE TABLE IF NOT EXISTS `tag_collections` (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
    slug            VARCHAR(31) UNIQUE,
    name            VARCHAR(31)
);


CREATE TABLE IF NOT EXISTS `tags` (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
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


