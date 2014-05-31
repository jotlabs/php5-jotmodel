<?php
namespace JotModel;

use PHPUnit_Framework_TestCase;

class DataSourceFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $factory;


    public function setUp()
    {
        $this->factory = DataSourceFactory::getInstance();
    }


    public function testFactoryExists()
    {
        $this->assertTrue(class_exists('JotModel\DataSourceFactory'));
        $this->assertNotNull($this->factory);
        $this->assertTrue(is_a($this->factory, 'JotModel\DataSourceFactory'));
    }


    public function testGetDataSourceReturnsPdoDataSource()
    {
        $dataSource = $this->factory->getDataSource('');
        $this->assertNotNull($dataSource);
        $this->assertTrue(is_a($dataSource, 'JotModel\DataSource'));
        $this->assertTrue(is_a($dataSource, 'JotModel\DataSources\PdoDataSource'));
    }
}
