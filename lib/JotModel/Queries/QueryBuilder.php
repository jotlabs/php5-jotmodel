<?php
namespace JotModel\Queries;

use JotModel\Queries\Query;

class QueryBuilder
{
    protected $modelClass;
    protected $queryName;
    protected $filters = array();


    public function build()
    {
        $query = new Query();
        $query->setModelClass($this->modelClass);
        $query->setQueryName($this->queryName);
        $query->setFilters($this->filters);
        return $query;
    }


    public function setQueryName($queryName)
    {
        $this->queryName = $queryName;
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
