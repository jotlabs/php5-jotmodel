<?php
namespace JotModel\Queries\Sql\Statements;

class UpdateStatement
{
    protected $statement;
    protected $name;

    public function __construct()
    {

    }


    public function setStatement($statement)
    {
        $this->statement = $statement;
    }


    public function getStatement()
    {
        return $this->statement;
    }


    public function setQueryName($name)
    {
        $this->name = $name;
    }


    public function getQueryName()
    {
        return $this->name;
    }


    public function toString()
    {
        return $this->getStatement();
    }
}
