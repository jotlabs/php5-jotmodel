<?php
namespace JotModel\Queries\Sql;

use JotModel\Queries\Sql\SqlQuery;
use JotModel\Queries\Sql\HydrateSqlQuery;

class SqlQueryBuilder
{
    const CONTENT_ENVELOPE_CLASS = 'JotModel\Models\ContentEnvelope';

    protected $queryType = '';
    protected $query;
    protected $modelClass;
    protected $modelName;
    protected $queryName;
    protected $filters;
    protected $limits;

    protected $sqlFields;
    protected $sqlJoins;
    protected $sqlHydrates;

    protected $toHydrate;
    protected $forAttribute;
    protected $filterProperties = array();


    public function __construct()
    {
        $this->tableName = '';
        $this->models    = array();
        $this->filters   = array();
        $this->sortOrder = array();

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
        $this->processSqlFilters($this->modelClass);
        $this->processSqlHydrates($this->modelClass);
        $this->processContentEnvelope($this->modelClass);

        $query = null;

        if ($this->queryType === 'hydrate') {
            $query = new HydrateSqlQuery();
            $query->setForAttribute($this->forAttribute);
            $query->setFilterProperties($this->filterProperties);

        } else {
            $query = new SqlQuery();
        }

        $query->setModelClass($this->modelClass);
        $query->setTable($this->tableName);
        $query->setQueryName("{$this->modelName}|{$this->queryName}");
        $query->setFilters($this->filters);

        if (!empty($this->limits)) {
            $query->setLimits($this->limits);
        }

        if (!empty($this->sortOrder)) {
            $query->setSort($this->sortOrder);
        }

        $query->setFields($this->sqlFields);
        $query->setJoins($this->sqlJoins);
        $query->setHydrates($this->sqlHydrates);
        $query->setLimits($this->limits);

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
        $this->limits     = $this->query->getRange();
        $this->sortOrder  = $this->query->getSortOrder();

        return $this;
    }


    public function setQueryType($queryType)
    {
        $this->queryType = $queryType;
        return $this;
    }


    public function setForAttribute($attributeName)
    {
        $this->forAttribute = $attributeName;
        return $this;
    }


    public function setFilterProperties($filterProperties)
    {
        $this->filterProperties = $filterProperties;
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


    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }


    protected function processSqlModelName($modelClass)
    {
        $this->modelName = $modelClass::$MODEL;

        if (!$this->tableName) {
            $this->tableName = $this->modelName;
        }
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
        $sqlJoins   = array();
        $fragments  = $modelClass::$SQL_FRAGMENTS;

        if (array_key_exists('joins', $fragments)) {
            $sqlJoins = $fragments['joins'];

            if (!is_array($sqlJoins)) {
                $sqlJoins = array($sqlJoins);
            }
        }

        $this->sqlJoins = array_merge($this->sqlJoins, $sqlJoins);
    }


    protected function processSqlFilters($modelClass)
    {
        // TODO: Replace this hacky with something schema based
        if (array_key_exists('tag', $this->filters)) {
            $join = 'INNER JOIN `tagged_content` AS `tc` ON tc.envelopeId = ce.envelopeId';
            $this->sqlJoins[] = $join;
        }

        if (array_key_exists('category', $this->filters)) {
            $join = 'INNER JOIN `category_content` AS `cc` ON cc.envelopeId = ce.envelopeId';
            $this->sqlJoins[] = $join;
        }
    }


    protected function processContentEnvelope($modelClass)
    {
        if (is_subclass_of($modelClass, self::CONTENT_ENVELOPE_CLASS)) {
            $envelopeClass = self::CONTENT_ENVELOPE_CLASS;

            // Add content envelope join
            $this->sqlJoins[] = "INNER JOIN `content_envelope` AS `ce` ON "
                                . "(ce.model = 'video' AND {$this->tableName}.id = ce.contentId)";

            // Add content_envelope fields
            $envelopeFields = $envelopeClass::$SQL_FIELDS;

            foreach ($envelopeFields as $property => $sqlField) {
                if (strpos($sqlField, '@') === 0) {
                    // hydrated field, do nothing.
                    //$this->toHydrate[$property] = substr($sqlField, 1);
                } elseif (!$sqlField || $property === $sqlField) {
                    $this->sqlFields[] = $property;
                } else {
                    $this->sqlFields[] = "{$sqlField} AS {$property}";
                }
            }

            //echo "Checking for hydrates: \n";
            $envelopeDecorators = $envelopeClass::$DECORATORS;

            if (!empty($envelopeDecorators)) {
                $envelopeFragments  = $envelopeClass::$SQL_FRAGMENTS;

                foreach ($envelopeDecorators as $decorator) {
                    if (array_key_exists($decorator, $envelopeFragments['hydrate'])) {
                        //echo "Adding envelope hydrates\n";
                        $hydrateSpec = $envelopeFragments['hydrate'][$decorator];
                        //print_r($hydrateSpec);

                        $sqlQuery = $this->createHydrateQuery($decorator, $hydrateSpec);
                        $this->sqlHydrates[] = $sqlQuery;
                    }
                }
            }
        }
    }


    protected function processSqlHydrates($modelClass)
    {
        $fragments    = $modelClass::$SQL_FRAGMENTS;
        $hydrateSpecs = $fragments['hydrate'];

        //echo "Model Name: {$this->modelName}\n";

        foreach ($this->toHydrate as $field => $table) {
            if (array_key_exists($table, $hydrateSpecs)) {
                $hydrateSpec = $hydrateSpecs[$table];

                if (empty($hydrateSpec['tableName'])) {
                    $hydrateSpec['tableName'] = $table;
                }

                $sqlQuery = $this->createHydrateQuery($field, $hydrateSpec);
                $this->sqlHydrates[] = $sqlQuery;
            }
        }

        //echo "SQL Hydrates for {$modelClass}: ";
        //print_r($this->sqlHydrates);
    }


    protected function createHydrateQuery($hydrateModel, $hydrateSpec)
    {
        // Need to get these:
        $queryName = "getBy" .  ucfirst('envelopeId');

        $modelClass = $hydrateSpec['modelClass'];
        $table      = $hydrateSpec['tableName'];

        $builder = new SqlQueryBuilder();
        $builder
            ->setQueryType('hydrate')
            ->setForAttribute($hydrateModel)
            ->setModelClass($modelClass)
            ->setTableName($table)
            ->setQueryName($queryName);

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
            $builder->setFilterProperties($hydrateSpec['properties']);
        }

        //echo "Builder: ";
        //print_r($builder);

        $sqlQuery = $builder->build();

        //echo "SQL Query: ";
        //print_r($sqlQuery);
        //echo "SQL: ", $sqlQuery->toString(), "\n";

        return $sqlQuery;
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
