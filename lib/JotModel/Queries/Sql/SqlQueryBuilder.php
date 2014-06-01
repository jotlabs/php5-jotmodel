<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\SqlQuery;

class SqlQueryBuilder
{
    protected $models;
    protected $query;


    public function __construct()
    {
        $this->models = array();
    }


    public function build()
    {
        $query = new SqlQuery();
        $query->setTable($this->query->getModel());
        $query->setFilters($this->query->getFilters());
        return $query;
    }


    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }
}
