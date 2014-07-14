<?php
namespace JotModel\DataSources;

use PHPUnit_Framework_TestCase;
use PDO;

class PdoDataSourceTest extends PHPUnit_Framework_TestCase
{
    public static $dsn        = 'sqlite::memory:';
    //public static $dsn        = 'sqlite:tmp/testdb.db';
    public static $schemaFile = '/var/testdata/unittest-schema.sql';
    public static $dataFile   = '/var/testdata/unittest-data.sql';
    protected $dataSource;


    public function setUp()
    {
        $db = $this->createDb();
        $this->dataSource = new PdoDataSource($db);
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


    protected function createDb()
    {
        $db = new PDO(self::$dsn);

        $sql = file_get_contents(PROJECT_ROOT . self::$schemaFile);
        $db->exec($sql);

        $sql = file_get_contents(PROJECT_ROOT . self::$dataFile);
        $db->exec($sql);

        return $db;
    }
}
