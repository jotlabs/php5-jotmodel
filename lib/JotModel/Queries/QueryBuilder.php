<?php
namespace JotModel\Queries;

use JotModel\Queries\Query;

class QueryBuilder
{
    protected $modelClass;
    protected $queryName;
    protected $filters = array();

    protected $rangeStart;
    protected $rangeLength;


    public function build()
    {
        $query = new Query();
        $query->setModelClass($this->modelClass);
        $query->setQueryName($this->queryName);
        $query->setFilters($this->filters);

        if ($this->rangeStart || $this->rangeLength) {
            $query->setRange($this->rangeStart, $this->rangeLength);
        }

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
        return $this;
    }


    public function setRange($start, $length)
    {
        $this->rangeStart  = intval($start);
        $this->rangeLength = intval($length);
        return $this;
    }
}
