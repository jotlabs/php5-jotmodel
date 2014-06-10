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


    public function setSchema($schema)
    {
        $this->schema = $schema;
    }


    public function findOne($query, $hydrate = true)
    {
        $first   = null;
        $results = $this->find($query, $hydrate);

        if (count($results)) {
            $first = $results[0];
        }

        return $first;
    }


    public function find($query, $hydrate = true)
    {
        $sqlQuery  = $this->getSqlQuery($query);
        $statement = $this->getStatement($sqlQuery);
        $params    = $this->getParameters($query);
        $results   = $this->runQuery($statement, $params);

        $hydrates = $sqlQuery->getHydrates();
        if ($hydrate && !empty($hydrates)) {
            //echo "Need to hydrate!\n";
        }

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


    protected function getSqlQuery($query)
    {

        $sqlBuilder = new SqlQueryBuilder();
        $sqlBuilder->setQuery($query);
        $sqlQuery   = $sqlBuilder->build();
        //print_r($sqlQuery);
        return $sqlQuery;
    }


    protected function getStatement($sqlQuery)
    {
        $statement = null;

        $sql = $sqlQuery->toString();
        //echo "SQL: {$sql}\n";
        $statement = $this->db->prepare($sql);

        // Set fetch-mode
        if ($sqlQuery->getModelClass()) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $sqlQuery->getModelClass());
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }

        return $statement;
    }
}
