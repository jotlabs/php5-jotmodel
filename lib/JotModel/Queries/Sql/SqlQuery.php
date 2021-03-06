<?php
namespace JotModel\Queries\Sql;

class SqlQuery
{
    protected $modelClass;
    protected $queryName;

    protected $table;
    protected $fields;
    protected $joins;
    protected $filters;
    protected $queryStructure;

    protected $hydrates;

    protected $groups;
    protected $limits;
    protected $sort;

    protected $paramList;


    public function __construct()
    {
        $this->fields   = array();
        $this->joins    = array();
        $this->filters  = array();
        $this->hydrates = array();
        $this->sort     = array();

        $this->paramList = array();
    }


    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }


    public function getModelClass()
    {
        return $this->modelClass;
    }


    public function setQueryName($queryName)
    {
        $this->queryName = $queryName;
    }


    public function getQueryName()
    {
        return $this->queryName;
    }


    public function setTable($tableName)
    {
        $this->table = $tableName;
    }


    public function setFilters($filters)
    {
        $this->filters = $filters;
    }


    public function setFields($fields)
    {
        $this->fields = $fields;
    }


    public function setHydrates($hydrates)
    {
        $this->hydrates = $hydrates;
    }


    public function hasHydrates()
    {
        return !empty($this->hydrates);
    }


    public function getHydrates()
    {
        return $this->hydrates;
    }


    public function setJoins($joins)
    {
        $this->joins = $joins;
    }


    public function setLimits($limits)
    {
        $this->limits = $limits;
    }


    public function setSort($sort)
    {
        $this->sort = $sort;
    }


    public function setStructure($structure)
    {
        $this->queryStructure = $structure;
    }


    public function toString()
    {
        $sqlString = '';

        if ($this->queryStructure) {
            $sqlString = $this->generateSqlFromStructure();
        } else {
            $sqlString = $this->generateSql();
        }

        return $sqlString;
    }


    protected function generateSqlFromStructure()
    {
        $sqlString = $this->queryStructure;

        $fieldList = implode(", ", $this->fields);
        $joins     = implode(" ", $this->joins);

        $sqlString = preg_replace('/{fieldList}/', $fieldList, $sqlString);
        $sqlString = preg_replace('/{joins}/', $joins, $sqlString);

        return $sqlString;
    }


    protected function generateSql()
    {
        $fieldList = implode(",\n\t", $this->fields);
        $table     = $this->table;
        $joins     = implode("\n", $this->joins);
        $filters   = $this->formatFilters($this->filters);
        $groups    = '';
        $limit     = $this->formatLimits($this->limits);
        $sort      = $this->formatSort($this->sort);

        //$sql = "SELECT {$fieldList} FROM `{$table}` {$joins} {$filters} {$groups} {$limit}";
        //$sql = trim($sql) . ';';

        $sqlBuffer = array(
            "SELECT",
            "\t{$fieldList}",
            "FROM `{$table}`"
        );

        if ($joins) {
            $sqlBuffer[] = $joins;
        }

        if ($filters) {
            $sqlBuffer[] = $filters;
        }

        if ($sort) {
            $sqlBuffer[] = $sort;
        }

        if ($groups) {
            $sqlBuffer[] = $groups;
        }

        if ($limit) {
            $sqlBuffer[] = $limit;
        }

        $sql = implode("\n", $sqlBuffer) . ';';

        return $sql;
    }


    protected function formatFilters($filters)
    {
        $filters = array();

        foreach ($this->filters as $field => $value) {
            $sqlField  = $field;
            $bindField = preg_replace('/^\w+\./', '', $field);
            $filters[] = "{$sqlField} = :{$bindField}";
        }

        $filterString = (!empty($filters)? 'WHERE ' . implode(' AND ', $filters) : '');

        return $filterString;
    }


    protected function formatSort($sortOrder)
    {
        $clauses = array();

        foreach ($sortOrder as $sort) {
            $order = ($sort->inAscending ? 'ASC' : 'DESC');
            $clauses[] = "{$sort->field} {$order}";
        }

        $sortString = (!empty($clauses)? 'ORDER BY ' . implode(', ', $clauses) : '');
        return $sortString;
    }


    protected function formatLimits($limits)
    {
        $limitClause = '';

        if (!empty($limits['start']) || !empty($limits['length'])) {
            //$start  = intval($limits['start']);
            //$length = intval($limits['length']);
            //$limitClause = "LIMIT {$start},{$length}";
            $limitClause = "LIMIT :pageOffset, :pageLength";

            $this->paramList[] = ':pageOffset';
            $this->paramList[] = ':pageLength';
        }

        return $limitClause;
    }
}
