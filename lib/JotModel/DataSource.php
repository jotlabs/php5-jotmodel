<?php
namespace JotModel;

interface DataSource
{
    public function find($query);
    public function findOne($query);
}
