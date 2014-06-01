<?php
namespace JotModel\Queries\Sql;

class SqlQuery
{
    protected $table;
    protected $fields;
    protected $joins;
    protected $filters;
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


    public function toString()
    {
        $fieldList = '*';
        $table     = $this->table;
        $joins     = '';
        $filters   = $this->formatFilters($this->filters);
        $groups    = '';
        $limit     = '';

        $sql = "SELECT {$fieldList} FROM `{$table}` {$joins} {$filters} {$groups} {$limit}";
        $sql = trim($sql) . ';';
        return $sql;
    }


    protected function formatFilters($filters)
    {
        $filters = array();

        foreach ($this->filters as $field => $value) {
            $filters[] = "{$field} = :{$field}";
        }

        $filterString = (!empty($filters)?'WHERE ':'') . implode(' AND ', $filters);

        return $filterString;
    }
}
