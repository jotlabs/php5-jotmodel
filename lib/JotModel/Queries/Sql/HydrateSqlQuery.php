<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\SqlQuery;

class HydrateSqlQuery extends SqlQuery
{
    protected $forAttribute;
    protected $filterProperties = array();


    public function setForAttribute($attributeName)
    {
        $this->forAttribute = $attributeName;
    }


    public function getForAttribute()
    {
        return $this->forAttribute;
    }


    public function setFilterProperties($filterProperties)
    {
        $this->filterProperties = $filterProperties;
    }


    public function getFilterProperties()
    {
        return $this->filterProperties;
    }
}
