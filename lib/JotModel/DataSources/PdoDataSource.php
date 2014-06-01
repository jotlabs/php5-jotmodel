<?php
namespace JotModel\DataSources;

use PDO;
use JotModel\DataSource;
use JotModel\Queries\Sql\SqlQueryBuilder;
use JotModel\Exceptions\JotModelException;

class PdoDataSource implements DataSource
{
    protected $db;

    protected $schema;
    protected $stmCache = array();


    public function __construct($db)
    {
        $this->setDbConnection($db);
    }


    public function setDbConnection($db)
    {
        if (is_a($db, 'PDO')) {
            $this->db = $db;
        } else {
            throw new JotModelException("PdoDataSource requires a PDO object");
        }
    }


    public function findOne($query)
    {
        $first   = null;
        $results = $this->find($query);

        if (count($results)) {
            $first = $results[0];
        }

        return $first;
    }

    public function find($query)
    {
        $statement = $this->getStatement($query);
        $params    = $this->getParameters($query);
        $results   = $this->runQuery($statement, $params);
        return $results;
    }


    protected function runQuery($statement, $params)
    {
        $statement->execute($params);
        $results = $statement->fetchAll();
        return $results;
    }


    protected function getParameters($query)
    {
        $params = null;

        foreach ($query->getFilters() as $field => $value) {
            $params[":{$field}"] = $value;
        }

        return $params;
    }


    protected function getStatement($query)
    {
        $statement = null;

        //echo "Query: ";
        //print_r($query);

        // Create SQL: which query, which filters, which joins
        $builder  = new SqlQueryBuilder();
        $builder->setQuery($query);
        $sqlQuery = $builder->build();

        //echo "SQL Query:";
        //print_r($sqlQuery);

        $sql = $sqlQuery->toString();
        $statement = $this->db->prepare($sql);

        // Set fetch-mode
        if ($query->getModelClass()) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $query->getModelClass());
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }

        return $statement;
    }
}
