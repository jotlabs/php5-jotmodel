<?php
namespace JotModel\Models;

class Category
{
    public static $MODEL = 'category_content';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'categoryBySlug' => 'SELECT * FROM `categories` WHERE slug = :slug;'
        ),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'       => '',
        'categoryId'       => '',
        'isPrimary'        => '',
        'category'         => '',
        'name'             => '',
        'collectionId'     => '',
        'collectionSlug'   => '',
        'collectionName'   => '',
        'collectionWeight' => '',
        'dateAdded'        => '',
    );

    protected $envelopeId;
    protected $categoryId;
    public $category;
    public $name;
    public $isPrimary;

    protected $collectionId;
    public $collectionSlug;
    public $collectionName;
    public $collectionWeight;

    public $dateAdded;
}
