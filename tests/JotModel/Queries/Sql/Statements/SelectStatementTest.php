<?php
namespace JotModel\Queries\Sql\Statements;

use PHPUnit_Framework_TestCase;

class SelectStatementTest extends PHPUnit_Framework_TestCase
{
    protected $statement;

    public function setUp()
    {
        $this->statement = new SelectStatement();
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModel\Queries\Sql\Statements\SelectStatement'));
        $this->assertNotNull($this->statement);
        $this->assertTrue(is_a($this->statement, 'JotModel\Queries\Sql\Statements\SelectStatement'));
    }


    /**
    * @expectedException JotModel\Exceptions\JotModelException
    * @expectedException JotModel\Exceptions\SqlException
    **/
    public function testEmptySelectThrowsSqlException()
    {
        $sql = $this->statement->toString();
    }


    public function testTableOnlyReturnsSimpleSqlQuery()
    {
        $this->statement->setTable('mytable');
        $sql = $this->statement->toString();
        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== -1);
        $this->assertTrue(preg_match('/^SELECT\s+\*\s+FROM\s+`mytable`;$/', $sql) === 1);
    }
}
