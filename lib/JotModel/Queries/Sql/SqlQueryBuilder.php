<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\SqlQuery;

class SqlQueryBuilder
{
    protected $query;
    protected $modelClass;
    protected $modelName;
    protected $queryName;
    protected $filters;

    protected $sqlFields;
    protected $sqlJoins;
    protected $sqlHydrates;

    protected $toHydrate;

    public function __construct()
    {
        $this->tableName = '';
        $this->models    = array();
        $this->filters   = array();

        $this->sqlFields   = array();
        $this->sqlJoins    = array();
        $this->sqlHydrates = array();
        $this->toHydrate   = array();
    }


    public function build()
    {
        $this->processSqlModelName($this->modelClass);
        $this->processSqlFields($this->modelClass);
        $this->processSqlJoins($this->modelClass);
        $this->processSqlHydrates($this->modelClass);

        $query = new SqlQuery();

        $query->setModelClass($this->modelClass);
        $query->setTable($this->tableName);
        $query->setFilters($this->filters);

        $query->setFields($this->sqlFields);
        $query->setJoins($this->sqlJoins);
        $query->setHydrates($this->sqlHydrates);

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
        $this->filters    = $this->query->getFilters();

        return $this;
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


    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }


    public function setJoins($joins)
    {
        $this->sqlJoins = array_merge($this->sqlJoins, $joins);
    }


    public function setFilters($filters)
    {
        $this->filters = array_merge($this->filters, $filters);
    }


    protected function processSqlModelName($modelClass)
    {
        $this->modelName = $modelClass::$MODEL;
    }


    protected function processSqlHydrates($modelClass)
    {
        $fragments    = $modelClass::$SQL_FRAGMENTS;
        $hydrateSpecs = $fragments['hydrate'];

        //echo "Model Name: {$this->modelName}\n";

        foreach ($this->toHydrate as $field => $table) {
            if (array_key_exists($table, $hydrateSpecs)) {
                $hydrateSpec = $hydrateSpecs[$table];

                //echo "Hydrate spec for {$table}: ";
                //print_r($hydrateSpec);

                $builder = new SqlQueryBuilder();
                $builder
                    ->setModelClass($hydrateSpec['modelClass'])
                    ->setTableName($table)
                    ->setQueryName("{$this->modelName}.{$table}");

                if (array_key_exists('join', $hydrateSpec)) {
                    $join = $hydrateSpec['join'];
                    if (!is_array($join)) {
                        $join = array($join);
                    }
                    $builder->setJoins($join);
                }

                if (array_key_exists('where', $hydrateSpec)) {
                    $filters = $hydrateSpec['where'];
                    if (!is_array($filters)) {
                        $filters = array($filters);
                    }
                    $builder->setFilters($filters);
                }

                //echo "Builder: ";
                //print_r($builder);

                $sqlQuery = $builder->build();

                //echo "SQL Query: ";
                //print_r($sqlQuery);
                //echo "SQL: ", $sqlQuery->toString(), "\n";

                $this->sqlHydrates[] = $sqlQuery;
            }
        }

        //echo "SQL Hydrates for {$modelClass}: ";
        //print_r($this->sqlHydrates);
    }


    protected function processSqlFields($modelClass)
    {
        $modelFields = $modelClass::$SQL_FIELDS;

        foreach ($modelFields as $property => $sqlField) {
            if (strpos($sqlField, '@') === 0) {
                // hydrated field, do nothing.
                $this->toHydrate[$property] = substr($sqlField, 1);
            } elseif (!$sqlField || $property === $sqlField) {
                $this->sqlFields[] = $property;
            } else {
                $this->sqlFields[] = "{$sqlField} AS {$property}";
            }
        }
    }


    protected function processSqlJoins($modelClass)
    {
        $fragments  = $modelClass::$SQL_FRAGMENTS;

        if (array_key_exists('joins', $fragments)) {
            $sqlJoins = $fragments['joins'];

            if (!is_array($sqlJoins)) {
                $sqlJoins = array($sqlJoins);
            }
        }

        $this->sqlJoins = array_merge($this->sqlJoins, $sqlJoins);
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
