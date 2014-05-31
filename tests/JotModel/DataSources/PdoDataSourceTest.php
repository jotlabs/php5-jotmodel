<?php
namespace JotModel\DataSources;

use PHPUnit_Framework_TestCase;
use PDO;

class PdoDataSourceTest extends PHPUnit_Framework_TestCase
{
    protected $dataSource;


    public function setUp()
    {
        $pdoDb = new PDO('sqlite::memory:');
        $this->dataSource = new PdoDataSource($pdoDb);
    }

    public function testPdoDataSourceExists()
    {
        $this->assertTrue(class_exists('JotModel\DataSources\PdoDataSource'));
        $this->assertNotNull($this->dataSource);
        $this->assertTrue(is_a($this->dataSource, 'JotModel\DataSources\PdoDataSource'));
    }

    /**
     * @expectedException JotModel\Exceptions\JotModelException
     */
    public function testCreateWithoutPdoThrowsException()
    {
        $db = (object) null;
        $dataSource = new PdoDataSource($db);
        $this->assertFalse(true);
    }
}
