<?php
namespace JotModel\Queries;

class Query
{
    protected $model;
    protected $modelClass;
    protected $filters;

    public function __construct()
    {
        $this->filters = array();
    }


    public function getModel()
    {
        return $this->model;
    }


    public function setModel($model)
    {
        $this->model = $model;
    }


    public function getModelClass()
    {
        return $this->modelClass;
    }


    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }


    public function getFilters()
    {
        return $this->filters;
    }


    public function setFilters($filters)
    {
        $this->filters = $filters;
    }
}
