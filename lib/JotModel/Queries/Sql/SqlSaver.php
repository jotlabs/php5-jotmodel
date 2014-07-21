<?php
namespace JotModel\Queries\Sql;

class SqlSaver
{
    protected $model;


    public function __construct($model = null)
    {
        if ($model) {
            $this->setModel($model);
        }
    }


    public function setModel($model)
    {
        $this->model = $model;
    }


    public function hasNextQuery()
    {
        return false;
    }


    public function nextQuery()
    {
        return $this->next();
    }


    protected function init()
    {
        $modelClass = get_class($model);
        echo "Model Class: {$modelClass}\n";
    }
}
