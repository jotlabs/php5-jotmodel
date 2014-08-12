<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\Statements\InsertStatement;
use JotModel\Queries\QueryBuilder;
use JotModel\Repositories\ContentRepository;
use JotModel\Models\ContentEnvelope;

abstract class SqlContentSaver
{
    const STATUS_ACTIVE = 1;

    protected $dataSource;

    private $typeModels  = array();
    private $contQueries = array(
        'saveEnvelope'   => 'INSERT INTO `content` VALUES(NULL, :statusId, :modelId, :contentId, :slug, :title, :excerpt, :extra1, :extra2, :permalink, :image, :dateAdded, :dateUpdated, :version, :score);',

        // Category Saver
        'saveEnvelopeCategory' => 'INSERT INTO `content_categories` VALUES(:contentId, :categoryId, :dateAdded);',
        'saveCategory' => 'INSERT INTO `categories` VALUES(NULL, :collectionId, :slug, :name);'

        // Tag Saver
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

        $statusId  = self::STATUS_ACTIVE;
        $modelName = $model::$MODEL_TYPE;
        $typeModel = $this->getTypeModel($modelName);
        $modelId   = ($typeModel) ? $typeModel->getId() : 0;

        $now = date('c');

        // How do we get the parameters needed?
        $params = array(
            ':statusId'    => $statusId,
            ':modelId'     => $modelId,
            ':contentId'   => $contentId,
            ':slug'        => $model->slug,
            ':title'       => $model->title,
            ':excerpt'     => $model->excerpt,
            ':extra1'      => $model->extra1,
            ':extra2'      => $model->extra2,
            ':permalink'   => ($model->permalink)   ? $model->permalink : $model->slug,
            ':image'       => $model->imageTemplate,
            ':dateAdded'   => ($model->dateAdded)   ? $model->dateAdded : $now,
            ':dateUpdated' => ($model->dateUpdated) ? $model->dateUpdated : ($model->dateAdded)
                                                    ? $model->dateAdded : $now,
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


    protected function saveContentTags($envelopeId, $modelTags)
    {
        //echo "Saving Content Tags for {$envelopeId}\n";
    }


    protected function saveContentCategories($envelopeId, $modelCategories)
    {
        $response = false;
        $now = date('c');

        $stmName = 'saveEnvelopeCategory';
        $insert  = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->contQueries['saveEnvelopeCategory']);

        foreach ($modelCategories as $slug => $title) {
            $category   = (object) array(
                'slug'         => $slug,
                'title'        => $title
            );

            $categoryId = $this->saveCategory($category);

            if ($categoryId) {
                $params = array(
                    ':contentId'  => $envelopeId,
                    ':categoryId' => $categoryId,
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
            $insert->setStatement($this->contQueries['saveCategory']);

            $params = array(
                ':slug'         => $category->slug,
                ':name'         => $category->title,
                ':collectionId' => 1
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
