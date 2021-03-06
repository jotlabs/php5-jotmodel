<?php
namespace JotModel\Queries\Sql;

use PHPUnit_Framework_TestCase;
use JotModel\Queries\QueryBuilder;

class SqlQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $builder;


    public function setUp()
    {
        $this->builder = new SqlQueryBuilder();
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModel\Queries\Sql\SqlQueryBuilder'));
        $this->assertNotNull($this->builder);
        $this->assertTrue(is_a($this->builder, 'JotModel\Queries\Sql\SqlQueryBuilder'));
    }


    public function testContentEnvelopeQueryStructureReturnsSqlQuery()
    {
        $slug = 'the-ashtanga-primary-series';
        $queryBuilder = new QueryBuilder();
        $queryBuilder
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);
        $query = $queryBuilder->build();


        $this->builder->setQuery($query);
        $sqlQuery = $this->builder->build();
        $this->assertNotNull($sqlQuery);
        //print_r($sqlQuery);
        $sql = $sqlQuery->toString();
        //echo "SQL: {$sql}\n";

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT ') === 0);
        $this->assertTrue(preg_match('/FROM `content_envelope`/', $sql) == 1);
        $this->assertTrue(preg_match('/WHERE slug = :slug/', $sql) == 1);
    }
}
