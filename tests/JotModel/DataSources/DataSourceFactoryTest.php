<?php
namespace JotModel\DataSources;

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
        $this->assertTrue(class_exists('JotModel\DataSources\DataSourceFactory'));
        $this->assertNotNull($this->factory);
        $this->assertTrue(is_a($this->factory, 'JotModel\DataSources\DataSourceFactory'));
    }
}
