<?php
namespace JotModel\Queries;

class Query
{
    protected $queryName;
    protected $modelClass;
    protected $filters;


    public function __construct()
    {
        $this->filters = array();
    }


    public function getQueryName()
    {
        return $this->queryName;
    }


    public function setQueryName($queryName)
    {
        $this->queryName = $queryName;
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
