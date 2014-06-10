<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\SqlQuery;

class SqlQueryBuilder
{
    protected $query;
    protected $modelClass;
    protected $queryName;

    public function __construct()
    {
        $this->models = array();
    }


    public function build()
    {
        $query = new SqlQuery();

        $query->setTable($this->getTableName());
        $query->setFilters($this->query->getFilters());
        $query->setFields($this->getFields());
        $query->setJoins($this->getJoins());

        $queryStructure = $this->getQueryStructure();
        if ($queryStructure) {
            $query->setStructure($queryStructure);
        }

        return $query;
    }


    public function setQuery($query)
    {
        $this->query = $query;

        $this->modelClass = $this->query->getModelClass();
        $this->queryName  = $this->query->getQueryName();

        return $this;
    }


    protected function getTableName()
    {
        $tableName = null;

        if ($this->modelClass) {
            $modelClass = $this->modelClass;
            $tableName  = $modelClass::$MODEL;
        }

        return $tableName;
    }


    protected function getFields()
    {
        $modelClass = $this->modelClass;
        $modelFields = $modelClass::$SQL_FIELDS;

        $sqlFields = array();
        foreach ($modelFields as $property => $sqlField) {
            if (strpos($sqlField, '@') === 0) {
                // hydrated field, do nothing.
            } elseif (!$sqlField || $property === $sqlField) {
                $sqlFields[] = $property;
            } else {
                $sqlFields[] = "{$sqlField} AS {$property}";
            }
        }

        return $sqlFields;
    }


    protected function getJoins()
    {
        $sqlJoins   = array();
        $modelClass = $this->modelClass;
        $fragments  = $modelClass::$SQL_FRAGMENTS;

        if (array_key_exists('joins', $fragments)) {
            $sqlJoins = $fragments['joins'];

            if (!is_array($sqlJoins)) {
                $sqlJoins = array($sqlJoins);
            }
        }

        return $sqlJoins;
    }


    protected function getQueryStructure()
    {
        $queryStructure = null;
        $modelClass     = $this->modelClass;
        $fragments      = $modelClass::$SQL_FRAGMENTS;
        $modelQueries   = $fragments['queries'];

        if ($this->queryName && array_key_exists($this->queryName, $modelQueries)) {
            $queryStructure = $modelQueries[$this->queryName];
        }

        return $queryStructure;
    }
}
