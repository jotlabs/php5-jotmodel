<?php
namespace JotModel;

use PDO;
use JotModel\DataSources\PdoDataSource;

class DataSourceFactory
{
    private static $INSTANCE;

    protected $schema;


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
        $dataSource->setSchema($this->schema);
        return $dataSource;
    }
}
