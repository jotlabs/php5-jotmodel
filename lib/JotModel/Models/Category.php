<?php
namespace JotModel\Models;

class Category
{
    public static $MODEL = 'categories';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'categoryid'     => '',
        'category'       => '',
        'name'           => '',
        'collectionId'   => '',
        'collectionSlug' => '',
        'collectionName' => '',
        'dateAdded'      => '',
    );

    protected $envelopeId;
    protected $categoryId;
    public $category;
    public $name;

    protected $collectionId;
    public $collectionSlug;
    public $collectionName;

    public $dateAdded;
}
