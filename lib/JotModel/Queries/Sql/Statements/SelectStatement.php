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
    protected $tokens;


    public function __construct()
    {
        $this->fieldList    = array();
        $this->fromTable    = '';
        $this->joins        = array();
        $this->whereClauses = array();
        $this->tokens       = array();
    }


    public function table($tableName)
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


    public function field($field, $fieldAlias = null)
    {
        $fieldSpec = $field;
        if ($fieldAlias) {
            $fieldSpec = "{$field} AS {$fieldAlias}";
        }

        $this->fieldList[] = $fieldSpec;
        return $this;
    }


    public function fields($fields)
    {
        if (!empty($fields[0])) {
            $this->fieldList = array_merge($this->fieldList, $fields);
        } else {
            foreach ($fields as $alias => $source) {
                $this->fieldList[] = "{$source} AS {$alias}";
            }
        }
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


    public function where($whereSpec)
    {
        $this->whereClauses[] = $whereSpec;
        $this->extractTokens($whereSpec);
        return $this;
    }


    public function addWheres($whereSpecs)
    {
        $this->whereClauses = array_merge($this->whereClauses, $whereSpecs);
        return $this;
    }


    public function getTokens()
    {
        return $this->tokens;
    }


    protected function extractTokens($tokenString)
    {
        if (preg_match_all('/(\:\w+)/', $tokenString, $matches)) {
            foreach ($matches[1] as $tokenMatch) {
                if (!in_array($tokenMatch, $this->tokens)) {
                    $this->tokens[] = $tokenMatch;
                }
            }
        }
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
        $formattedFields = '*';

        if ($fieldList) {
            $formattedFields = implode(",\n\t", $this->fieldList);
        }

        return "\t{$formattedFields}";
    }


    protected function formatJoins($joinClauses)
    {
        return '';
    }


    protected function formatWheres($whereClauses)
    {
        $clause = '';

        if ($whereClauses) {
            $clause = "WHERE\n\t" . implode("\nAND\t", $whereClauses);
        }

        return $clause;
    }


    protected function formatLimits($limits)
    {
        return '';
    }
}
