<?php
namespace JotModel\Queries\Sql\Statements;

use JotModel\Exceptions\SqlException;

class SelectStatement
{
    protected $fieldList;
    protected $fromTable;
    protected $joinClauses;
    protected $whereClauses;
    protected $limits;


    public function __construct()
    {
        $this->fieldList    = array();
        $this->fromTable    = '';
        $this->joins        = array();
        $this->whereClauses = array();
    }


    public function setTable($tableName)
    {
        $this->fromTable = $tableName;
        return $this;
    }


    public function setLimits($offset, $length)
    {
        $this->limits = (object) array(
            'offset' => $offset,
            'length' => $length
        );
    }


    public function addField($fieldSpec)
    {
        $this->fieldList[] = $fieldSpec;
        return $this;
    }


    public function addFields($fieldSpecs)
    {
        $this->fieldList = array_merge($this->fieldList, $fieldSpecs);
        return $this;
    }


    public function addJoin($joinSpec)
    {
        $this->joinClauses[] = $joinSpec;
        return $this;
    }


    public function addJoins($joinSpecs)
    {
        $this->joinClauses = array_merge($this->joinClauses, $joinSpecs);
        return $this;
    }


    public function addWhere($whereSpec)
    {
        $this->whereSpec[] = $whereSpec;
        return $this;
    }


    public function addWheres($whereSpecs)
    {
        $this->whereClauses = array_merge($this->whereClauses, $whereSpecs);
        return $this;
    }



    public function toString()
    {
        $sql = $this->generateSql();
        return $sql;
    }


    protected function generateSql()
    {
        if (!$this->fromTable) {
            throw new SqlException("No table specified in SelectStatement.");
        }

        $fieldList = $this->formatFieldList($this->fieldList);
        $table     = $this->fromTable;
        $joins     = $this->formatJoins($this->joinClauses);
        $wheres    = $this->formatWheres($this->whereClauses);
        $groups    = '';
        $limit     = $this->formatLimits($this->limits);

        //$sql = "SELECT {$fieldList} FROM `{$table}` {$joins} {$filters} {$groups} {$limit}";
        //$sql = trim($sql) . ';';

        $sqlBuffer = array(
            "SELECT",
            "{$fieldList}",
            "FROM `{$table}`"
        );

        if ($joins) {
            $sqlBuffer[] = $joins;
        }

        if ($wheres) {
            $sqlBuffer[] = $wheres;
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


    protected function formatFieldList($fieldList)
    {
        $fieldList = '*';

        if ($this->fieldList) {
            $fieldList = implode(",\n\t", $this->fieldList);
        }

        return "\t{$fieldList}";
    }


    protected function formatJoins($joinClauses)
    {
        return '';
    }


    protected function formatWheres($whereClauses)
    {
        return '';
    }


    protected function formatLimits($limits)
    {
        return '';
    }
}
