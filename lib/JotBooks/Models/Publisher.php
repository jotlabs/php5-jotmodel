<?php
namespace JotBooks\Models;

class Publisher
{
    public static $MODEL='publisher';
    public static $SQL_FRAGMENTS = array(
        'queries' => array(),
        'hydrate' => array(),
        'joins'   => array()
    );
    public static $SQL_FIELDS = array(
        'id'   => '',
        'name' => '',
        'slug' => ''
    );

    protected $id;

    public $name;
    public $slug;
}
