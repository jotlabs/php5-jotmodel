<?php
namespace JotModel\Queries\Sql;


class SqlSaverFactory
{
    protected static $INSTANCE;


    protected function __construct()
    {
    }


    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            $className = get_called_class();
            self::$INSTANCE = new $className();
        }

        return self::$INSTANCE;
    }


    public function getSqlSaver($model)
    {
        $saver = null;

        if (!empty($model::$SQL_SAVER)) {
            $saverClass = $model::$SQL_SAVER;
            $saver      = new $saverClass($model);
        }

        return $saver;
    }
}
