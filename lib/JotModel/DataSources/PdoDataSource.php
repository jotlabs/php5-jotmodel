<?php
namespace JotModel\DataSources;

use PDO;
use JotModel\DataSource;
use JotModel\Queries\Sql\SqlQueryBuilder;
use JotModel\Queries\Sql\SqlSaverFactory;
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


    public function find($query, $hydrate = false)
    {
        $sqlQuery  = $this->getSqlQuery($query);
        $statement = $this->getStatement($sqlQuery);
        $params    = $this->getParameters($query);
        $results   = $this->runQuery($statement, $params);

        if ($results && $hydrate && $sqlQuery->hasHydrates()) {
            //echo "Need to hydrate!\n";
            $results = $this->hydrateResults($results, $sqlQuery);
        }

        return $results;
    }


    public function save($model)
    {
        $response = false;
        $factory  = SqlSaverFactory::getInstance();
        $saver    = $factory->getSqlSaver($model);

        if ($saver) {
            $saver->setDataSource($this);
            $response = $saver->save($model, $this);
        }

        return $response;
    }


    public function insert($insert, $params)
    {
        $statement = $this->getStatement($insert);
        if (!$this->isPdoError($statement)) {
            $response = $this->execQuery($statement, $params);
        }
        return $response;
    }


    public function update($update, $params)
    {
        $statement = $this->getStatement($update);
        if (!$this->isPdoError($statement)) {
            $response = $this->execQuery($statement, $params);
        }
        return $response;
    }


    protected function hydrateResults($results, $sqlQuery)
    {
        $hydratedResults = array();

        foreach ($results as $row) {
            $row = $this->hydrate($row, $sqlQuery);
            $hydratedResults[] = $row;
        }

        return $hydratedResults;
    }


    protected function hydrate($row, $sqlQuery)
    {
        $hydrates = $sqlQuery->getHydrates();

        foreach ($hydrates as $hydrateQuery) {
            //print_r($hydrateQuery);
            //print_r($row);

            // Create and run the SQL statement
            $statement = $this->getStatement($hydrateQuery);
            $params    = $this->getHydrateParameters($hydrateQuery, $row);
            $results   = $this->runQuery($statement, $params);

            //print_r($results);

            $attribute = $hydrateQuery->getForAttribute();
            $row->$attribute = $results;
        }

        return $row;
    }


    protected function getHydrateParameters($query, $row)
    {
        $filterProps = $query->getFilterProperties();
        $params = array();

        foreach ($filterProps as $filterName => $filterProperty) {
            $methodName = 'get' . ucfirst($filterProperty);
            $paramName  = ":{$filterName}";

            if (isset($row->$filterProperty)) {
                $params[$paramName] = $row->$filterProperty;
            } elseif (method_exists($row, $methodName)) {
                $params[$paramName] = $row->$methodName();
            } else {
                $className = get_class($row);
                echo "[ERROR-] Can't find row property {$filterProperty} in {$className}.\n";
            }
        }

        return $params;
    }


    protected function runQuery($statement, $params)
    {
        $results = array();
        $statement->execute($params);

        if (!$this->isPdoError($statement)) {
            $results = $statement->fetchAll();
        }

        return $results;
    }


    protected function execQuery($statement, $params)
    {
        $response = $statement->execute($params);
        $this->isPdoError($statement);
        return $response;
    }


    protected function getParameters($query)
    {
        $params = $this->applyPaginationParams($query);

        foreach ($query->getFilters() as $field => $value) {
            $params[":{$field}"] = $value;
        }

        return $params;
    }


    protected function applyPaginationParams($query)
    {
        $params = array();

        // Check for pagination ranges
        $range = $query->getRange();
        if (array_key_exists('start', $range) && is_integer($range['start'])) {
            $params[":pageOffset"] = intval($range['start']);
        }

        if (array_key_exists('length', $range) && is_integer($range['length'])) {
            $params[":pageLength"] = intval($range['length']);
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
        $queryName = $sqlQuery->getQueryName();

        if (array_key_exists($queryName, $this->stmCache)) {
            $statement = $this->stmCache[$queryName];
            //echo '*';

        } else {
            $sql = $sqlQuery->toString();
            //echo "SQL: {$sql}\n";
            $statement = $this->db->prepare($sql);

            if (!$this->isPdoError($statement)) {

                // Set fetch-mode
                if (method_exists($sqlQuery, 'getModelClass') && $sqlQuery->getModelClass()) {
                    $statement->setFetchMode(PDO::FETCH_CLASS, $sqlQuery->getModelClass());
                } else {
                    $statement->setFetchMode(PDO::FETCH_OBJ);
                }

                // Cache prepared statement
                $this->stmCache[$queryName] = $statement;
                //echo '!', $queryName;
            }
        }


        return $statement;
    }


    protected function isPdoError($statement)
    {
        $isError = false;

        if ($statement) {
            $errorCode = $statement->errorCode();
            if ($errorCode && $errorCode !== '00000') {
                $errorInfo = $statement->errorInfo();
                echo "[ERROR-] PDO Statement error {$errorCode}: {$errorInfo[2]}\n";
                $isError = true;
            }

        } else {
            $errorCode = $this->db->errorCode();
            if ($errorCode && $errorCode !== '00000') {
                $errorInfo = $this->db->errorInfo();
                echo "[ERROR-] PDO Database error {$errorCode}: {$errorInfo[2]}\n";
                $isError = true;
            }

        }

        return $isError;
    }
}
