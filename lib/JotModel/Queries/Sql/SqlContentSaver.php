<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\Statements\InsertStatement;
use JotModel\Queries\Sql\Statements\UpdateStatement;
use JotModel\Queries\QueryBuilder;
use JotModel\Repositories\ContentRepository;
use JotModel\Models\ContentEnvelope;

abstract class SqlContentSaver
{
    const STATUS_ACTIVE = 1;
    const STATUS_SITE   = 2;

    public static $statusList = array(
        'A' => 1,
        'Z' => 2
    );

    protected $dataSource;

    private $typeModels  = array();
    private $contQueries = array(
        // Content Envelope saver
        'saveEnvelope'   => 'INSERT INTO `content` VALUES(NULL, :statusId, :modelId, :contentId, :authorId, :slug, :title, :excerpt, :extra1, :extra2, :pageUrl, :permalink, :image, :dateAdded, :dateUpdated, :guid, :version, :score);',
        'updateEnvelope'   => 'UPDATE `content` SET slug = :slug, title = :title, excerpt = :excerpt, extra1 = :extra1, extra2 = :extra2, pageUrl = :pageUrl, imageTemplate = :image, dateUpdated = :dateUpdated, version = :version, score = :score WHERE envelopeId = :envelopeId;',

        // Author Saver
        'saveAuthor' => 'INSERT INTO `content_authors`  VALUES(NULL, :name, :slug, :shortBio, :image, :aboutSlug, :bio);',

        // Category Saver
        'saveEnvelopeCategory' => 'INSERT INTO `content_categories` VALUES(:contentId, :categoryId, :isPrimary, :dateAdded);',
        'saveCategory' => 'INSERT INTO `categories` VALUES(NULL, :collectionId, :slug, :name, :description, :totalArticles);',

        // Tag Saver
        'saveEnvelopeTag' => 'INSERT INTO `content_tags` VALUES(:contentId, :tagId, :dateAdded);',
        'saveTag' => 'INSERT INTO `tags` VALUES(NULL, :collectionId, :slug, :name, :description);'
    );


    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }


    abstract public function save($model);


    protected function saveEnvelope($model, $contentId)
    {
        $stmName = 'saveEnvelope';
        $insert  = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->contQueries['saveEnvelope']);

        $statusId  = $this->getStatusId($model->status);
        $modelName = $model::$MODEL_TYPE;
        $typeModel = $this->getTypeModel($modelName);
        $modelId   = ($typeModel) ? $typeModel->getId() : 0;
        $authorId  = $this->saveAuthor($model);

        $now = date('c');
        $pageUrl = ($model->permalink) ? $model->permalink : $model->slug;

        $dateAdded   = ($model->dateAdded)   ? $model->dateAdded : $now;
        $dateUpdated = ($model->dateUpdated) ? $model->dateUpdated : $dateAdded;

        // How do we get the parameters needed?
        $params = array(
            ':statusId'    => $statusId,
            ':modelId'     => $modelId,
            ':contentId'   => $contentId,
            ':authorId'    => $authorId,
            ':slug'        => $model->slug,
            ':title'       => $model->title,
            ':excerpt'     => $model->excerpt,
            ':extra1'      => $model->extra1,
            ':extra2'      => $model->extra2,
            ':pageUrl'     => $pageUrl,
            ':permalink'   => $pageUrl,
            ':image'       => $model->imageTemplate,
            ':dateAdded'   => $dateAdded,
            ':dateUpdated' => $dateUpdated,
            ':guid'        => $model->guid,
            ':version'     => ($model->version)     ? $model->version : 1,
            ':score'       => ($model->score)       ? $model->score : 0
        );

        //print_r($params);
        $response = $this->dataSource->insert($insert, $params);

        // TODO: Save decorators
        $envelopeId = $this->getEnvelopeIdBySlug($model->slug);
        if ($envelopeId) {
            $decorators   = $model::$DECORATORS;
            $methodPrefix = 'saveContent';

            foreach ($decorators as $decorator) {
                if (!empty($model->{$decorator})) {
                    $method = $methodPrefix . ucfirst($decorator);
                    if (method_exists($this, $method)) {
                        $this->{$method}($envelopeId, $model->{$decorator});
                    }

                }
            }
        }


        return $response;
    }


    protected function updateEnvelope($oldArticle, $newArticle)
    {
        $stmName = 'updateEnvelope';
        $update = new UpdateStatement();
        $update->setQueryName($stmName);
        $update->setStatement($this->contQueries[$stmName]);

        $pageUrl = ($newArticle->permalink) ? $newArticle->permalink : $newArticle->slug;

        $params = array(
            ':envelopeId'  => $oldArticle->getEnvelopeId(),
            ':slug'        => $newArticle->slug,
            ':title'       => $newArticle->title,
            ':excerpt'     => $newArticle->excerpt,
            ':extra1'      => $newArticle->extra1,
            ':extra2'      => $newArticle->extra2,
            ':pageUrl'     => $pageUrl,
            ':image'       => $newArticle->imageTemplate,
            ':dateUpdated' => $newArticle->dateUpdated,
            ':version'     => $oldArticle->version + 1,
            ':score'       => ($newArticle->score) ? $newArticle->score : $oldArticle->score
        );

        //print_r($params);
        $response = $this->dataSource->update($update, $params);

        return $response;
    }


    protected function getStatusId($status)
    {
        $statusId = self::STATUS_ACTIVE;

        if (!empty(self::$statusList[$status])) {
            $statusId = self::$statusList[$status];
        }

        return $statusId;
    }


    protected function saveAuthor($model)
    {
        $authorId = 1;
        $dbAuthor = $this->getAuthorBySlug($model->authorSlug);

        if (!$dbAuthor) {
            $stmName = 'saveAuthor';
            $insert = new InsertStatement();
            $insert->setQueryName($stmName);
            $insert->setStatement($this->contQueries[$stmName]);

            $params = array(
                ':name'      => $model->authorName,
                ':slug'      => $model->authorSlug,
                ':shortBio'  => $model->authorShortBio,
                ':image'     => $model->authorImage,
                ':aboutSlug' => $model->authorAboutSlug,
                ':bio'       => $model->authorBio
            );

            $response = $this->dataSource->insert($insert, $params);

            if ($response) {
                $dbAuthor = $this->getAuthorBySlug($model->authorSlug);
            }
        }

        if ($dbAuthor) {
            $authorId = $dbAuthor->getId();
        }

        return $authorId;
    }


    protected function getAuthorBySlug($authorSlug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\Author')
            ->setQueryName('getBySlug')
            ->filter('slug', $authorSlug);

        $query  = $builder->build();
        $author = $this->dataSource->findOne($query, false);

        return $author;
    }


    protected function saveContentCategories($envelopeId, $modelCategories)
    {
        $response = false;
        $now = date('c');

        $stmName = 'saveEnvelopeCategory';
        $insert  = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->contQueries[$stmName]);

        foreach ($modelCategories as $slug => $category) {
            $categoryId = $this->saveCategory($category);
            $isPrimary  = (!empty($category->isPrimary) && $category->isPrimary)?'Y':'N';

            if ($categoryId) {
                $params = array(
                    ':contentId'  => $envelopeId,
                    ':categoryId' => $categoryId,
                    ':isPrimary'  => $isPrimary,
                    ':dateAdded'  => $now
                );

                $response = $this->dataSource->insert($insert, $params);
            }
        }

        return $response;
    }


    protected function saveCategory($category)
    {
        $categoryId = $this->getCategoryBySlug($category->slug);

        if (!$categoryId) {
            $stmName = 'saveCategory';
            $insert  = new InsertStatement();
            $insert->setQueryName($stmName);
            $insert->setStatement($this->contQueries[$stmName]);

            $params = array(
                ':slug'          => $category->slug,
                ':name'          => $category->title,
                ':collectionId'  => 1, # FIXME
                ':description'   => empty($category->description) ? '' : $category->description,
                ':totalArticles' => 0
            );

            $response = $this->dataSource->insert($insert, $params);

            if ($response) {
                $categoryId = $this->getCategoryBySlug($category->slug);
            }
        }

        return $categoryId;
    }


    protected function getCategoryBySlug($categorySlug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\Category')
            ->setQueryName('categoryBySlug')
            ->filter('slug', $categorySlug);

        $query    = $builder->build();
        $category = $this->dataSource->findOne($query, false);

        return $category ? $category->id : null;
    }


    protected function saveContentTags($envelopeId, $modelTags)
    {
        $response = false;
        $now = date('c');

        $stmName = 'saveEnvelopeTag';
        $insert  = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->contQueries[$stmName]);

        foreach ($modelTags as $slug => $title) {
            $tag   = (object) array(
                'slug'         => $slug,
                'title'        => $title,
                'collectionId' => 1, # FIXME
                'description'  => '' # FIXME
            );

            $tagId = $this->saveTag($tag);

            if ($tagId) {
                $params = array(
                    ':contentId' => $envelopeId,
                    ':tagId'     => $tagId,
                    ':dateAdded' => $now
                );

                $response = $this->dataSource->insert($insert, $params);
            }
        }

        return $response;
    }


    protected function saveTag($tag)
    {
        $tagId = $this->getTagBySlug($tag->slug);

        if (!$tagId) {
            $stmName = 'saveTag';
            $insert  = new InsertStatement();
            $insert->setQueryName($stmName);
            $insert->setStatement($this->contQueries[$stmName]);

            $params = array(
                ':slug'         => $tag->slug,
                ':name'         => $tag->title,
                ':collectionId' => $tag->collectionId,
                ':description'  => $tag->description
            );

            $response = $this->dataSource->insert($insert, $params);

            if ($response) {
                $tagId = $this->getTagBySlug($tag->slug);
            }
        }

        return $tagId;
    }


    protected function getTagBySlug($tagSlug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\Tag')
            ->setQueryName('tagBySlug')
            ->filter('slug', $tagSlug);

        $query = $builder->build();
        $tag   = $this->dataSource->findOne($query, false);

        return $tag ? $tag->id : null;
    }


    protected function getTypeModel($modelName)
    {
        $typeModel = null;

        if (empty($this->typeModels)) {
            $builder = new QueryBuilder();
            $builder
                ->setModelClass('JotModel\Models\ContentModel')
                ->setQueryName('getAllModels');

            $query  = $builder->build();
            $models = $this->dataSource->find($query);

            foreach ($models as $model) {
                $this->typeModels[$model->slug] = $model;
            }

        }

        if (array_key_exists($modelName, $this->typeModels)) {
            $typeModel = $this->typeModels[$modelName];
        }

        return $typeModel;
    }


    protected function hasUpdated($newContent, $oldContent)
    {
        $sourceTs = strtotime($newContent->dateUpdated);
        $existTs = strtotime($oldContent->dateUpdated);
        return ($sourceTs > $existTs);
    }


    protected function getEnvelopeIdBySlug($slug)
    {
        $content    = $this->getContentBySlug($slug);
        $envelopeId = $content->getEnvelopeId();
        return $envelopeId;
    }


    protected function getContentBySlug($slug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);

        $query   = $builder->build();
        $content = $this->dataSource->findOne($query);

        return $content;
    }
}
