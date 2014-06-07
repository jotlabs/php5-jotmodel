<?php
namespace JotModel\Queries\Sql;

class SqlQuery
{
    protected $table;
    protected $fields;
    protected $joins;
    protected $filters;
    protected $queryStructure;

    protected $groups;
    protected $limit;


    public function __construct()
    {
        $this->fields  = array();
        $this->joins   = array();
        $this->filters = array();
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


    public function setJoins($joins)
    {
        $this->joins = $joins;
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
        $limit     = '';

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

        $filterString = (!empty($filters)?'WHERE ':'') . implode(' AND ', $filters);

        return $filterString;
    }
}
