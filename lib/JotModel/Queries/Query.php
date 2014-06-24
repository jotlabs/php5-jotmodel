<?php
namespace JotModel\Queries;

class Query
{
    protected $queryName;
    protected $modelClass;
    protected $filters;

    protected $rangeStart;
    protected $rangeLength;


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


    public function setRange($start, $length)
    {
        $this->rangeStart  = $start;
        $this->rangeLength = $length;
    }


    public function getRange()
    {
        return array(
            'start'  => $this->rangeStart,
            'length' => $this->rangeLength
        );
    }
}
