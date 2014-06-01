<?php
namespace JotModel\Queries;

use JotModel\Queries\Query;

class QueryBuilder
{
    protected $model;
    protected $modelClass;
    protected $filters = array();


    public function build()
    {
        $query = new Query();
        $query->setModel($this->model);
        $query->setModelClass($this->modelClass);
        $query->setFilters($this->filters);
        return $query;
    }


    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }


    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
        return $this;
    }


    public function filter($property, $value)
    {
        $this->filters[$property] = $value;
    }
}
