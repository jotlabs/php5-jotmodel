<?php
namespace JotModel\DataSources;

use PDO;
use JotModel\DataSource;
use JotModel\Exceptions\JotModelException;

class PdoDataSource implements DataSource
{
    protected $db;

    public function __construct($db)
    {
        if (is_a($db, 'PDO')) {
            $this->db = $db;
        } else {
            throw new JotModelException("PdoDataSource requires a PDO object");
        }
    }
}
