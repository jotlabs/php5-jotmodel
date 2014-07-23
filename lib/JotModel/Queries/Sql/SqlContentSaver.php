<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\Statements\InsertStatement;
use JotModel\Queries\QueryBuilder;

abstract class SqlContentSaver
{
    const STATUS_ACTIVE = 1;

    protected $dataSource;

    private $typeModels  = array();
    private $contQueries = array(
        'saveEnvelope'   => 'INSERT INTO `content` VALUES(NULL, :statusId, :modelId, :contentId, :slug, :title, :excerpt, :extra1, :extra2, :permalink, :image, :dateAdded, :dateUpdated, :version, :score);'
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
            ':permalink'   => $model->permalink,
            ':image'       => $model->imageTemplate,
            ':dateAdded'   => $model->dateAdded,
            ':dateUpdated' => $model->dateUpdated,
            ':version'     => $model->version,
            ':score'       => $model->score
        );

        //print_r($params);
        $response = $this->dataSource->insert($insert, $params);
        return $response;
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
}
