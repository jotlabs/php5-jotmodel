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
        $this->statement->table('mytable');
        $sql = $this->statement->toString();
        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== false);
        $this->assertTrue(preg_match('/^SELECT\s+\*\s+FROM\s+`mytable`;$/', $sql) === 1);
    }


    public function testAddedFieldAppearsInSqlQuery()
    {
        $this->statement
            ->table('mytable')
            ->field('myField');

        $sql = $this->statement->toString();

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== false);
        $this->assertTrue(preg_match('/^SELECT\s+myField\s+FROM\s+`mytable`;$/', $sql) === 1);

    }


    public function testAddedRenamedFieldAppearsInSqlQuery()
    {
        $this->statement
            ->table('mytable')
            ->field('myField', 'my_field');

        $sql = $this->statement->toString();

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== false);
        $this->assertTrue(preg_match('/^SELECT\s+myField\s+AS\s+my_field\s+FROM\s+`mytable`;$/', $sql) === 1);

    }


    public function testAddedFieldsAppearInSqlQuery()
    {
        $this->statement
            ->table('mytable')
            ->fields(array('field1', 'field2'));

        $sql = $this->statement->toString();

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== false);
        $this->assertTrue(preg_match('/^SELECT\s+field1,\s+field2\s+FROM\s+`mytable`;$/', $sql) === 1);

    }


    public function testAddedRenamedFieldsAppearInSqlQuery()
    {
        $this->statement
            ->table('mytable')
            ->fields(array(
                'f1' => 'a.f1',
                'f2' => 'b.f2'
            ));

        $sql = $this->statement->toString();
        //echo "SQL: {$sql}\n";

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `mytable`') !== false);
        $this->assertTrue(preg_match('/^SELECT\s+a\.f1 AS f1,\s+b\.f2 AS f2\s+FROM\s+`mytable`;$/', $sql) === 1);

    }


    public function testAddingWhereClauseAppearsinSqlQuery()
    {
        $this->statement
            ->fields(array('f1', 'f2'))
            ->table('t')
            ->where('f1 = :token');

        $sql = $this->statement->toString();
        //echo "SQL: {$sql}\n";

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `t`') !== false);
        $this->assertTrue(strpos($sql, 'WHERE') !== false);
        $this->assertTrue(strpos($sql, 'f1 = :token') !== false);
        $this->assertTrue(preg_match('/FROM `t`\s+WHERE\s+f1 = :token;$/', $sql) === 1);
    }


    public function testAddingMultipleWhereClauseAppearsinSqlQuery()
    {
        $this->statement
            ->fields(array('f1', 'f2'))
            ->table('t')
            ->where('f1 = :t1')
            ->where('f2 = :t2');

        $sql = $this->statement->toString();
        //echo "SQL: {$sql}\n";

        $this->assertNotNull($sql);
        $this->assertTrue(strpos($sql, 'SELECT') === 0);
        $this->assertTrue(strpos($sql, 'FROM `t`') !== false);
        $this->assertTrue(strpos($sql, 'WHERE') !== false);
        $this->assertTrue(strpos($sql, 'f1 = :t1') !== false);
        $this->assertTrue(strpos($sql, 'f2 = :t2') !== false);
        $this->assertTrue(preg_match('/FROM `t`\s+WHERE\s+f1 = :t1\s+AND\s+f2 = :t2;$/', $sql) === 1);
    }


    public function testAddWhereWithTokenListsTokens()
    {
        $this->statement
            ->fields(array('f1', 'f2'))
            ->table('t')
            ->where('f1 = :token');

        $tokens = $this->statement->getTokens();
        $this->assertNotNull($tokens);
        $this->assertTrue(in_array(':token', $tokens));
    }
}
