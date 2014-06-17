<?php
namespace JotModel;

use PDO;
use JotModel\DataSources\PdoDataSource;

class DataSourceFactory
{
    private static $INSTANCE;

    protected $schema;
    protected $config;
    protected $cache;


    protected function __construct()
    {
        $this->init();
    }


    public function getInstance()
    {
        if (self::$INSTANCE == null) {
            self::$INSTANCE = new self();
        }

        return self::$INSTANCE;
    }


    public function setConfig($config)
    {
        $this->config = $config;
    }


    public function getDataSource($dsName)
    {
        $dataSource = null;

        if ($dsName && array_key_exists($dsName, $this->cache)) {
            $dataSource = $this->cache[$dsName];

        } elseif ($this->hasDataSourceConfig($dsName)) {
            $config     = $this->getDataSourceConfig($dsName);
            $dataSource = $this->createDataSourceFromConfig($config);
            $this->cache[$dsName] = $dataSource;

        } elseif ($dsName === '__UNITTEST__') {
            $pdoConnection = new PDO('sqlite::memory:');
            $dataSource    = new PdoDataSource($pdoConnection);
            $dataSource->setSchema($this->schema);

        }

        return $dataSource;
    }


    protected function hasDataSourceConfig($dsName)
    {
        return property_exists($this->config, $dsName);
    }


    protected function getDataSourceConfig($dsName)
    {
        return $this->config->{$dsName};
    }


    protected function createDataSourceFromConfig($config)
    {
        $dataSource = null;

        if ($config->type === 'PDO') {
            $pdoConnection = null;

            if (isset($config->username) && isset($config->password)) {
                $pdoConnection = new PDO($config->dsn, $config->username, $config->password);
            } else {
                $pdoConnection = new PDO($config->dsn);
            }

            $dataSource = new PdoDataSource($pdoConnection);
            $dataSource->setSchema($this->schema);
        }

        return $dataSource;
    }


    protected function init()
    {
        $this->cache = array();
        $this->config = (object) array();
    }
}
