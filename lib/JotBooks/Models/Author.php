<?php
namespace JotBooks\Models;

class Author
{
    public static $MODEL='author';
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
