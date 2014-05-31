<?php
namespace JotModel\DataSources;

use JotModel\DataSources\Implementations\PdoDataSource;

class DataSourceFactory
{
    private static $INSTANCE;


    public function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new self();
        }

        return self::$INSTANCE;
    }


    public function getDataSource($dsName)
    {
        $dataSource = null;
        $dataSource = new PDO('sqlite::memory:');
        return $dataSource;
    }
}
