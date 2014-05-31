<?php
namespace JotModel\DataSources;

use PDO;
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
        $pdoConnection = new PDO('sqlite::memory:');
        $dataSource    = new PdoDataSource($pdoConnection);
        return $dataSource;
    }
}
