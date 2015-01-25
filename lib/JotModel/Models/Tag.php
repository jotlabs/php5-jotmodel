<?php
namespace JotModel\Models;

class Tag
{
    public static $MODEL = 'tagged_content';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'tagBySlug' => 'SELECT * FROM `tags` WHERE slug = :slug;'
        ),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'            => '',
        'tagId'                 => '',
        'tag'                   => '',
        'name'                  => '',
        'description'           => '',
        'collectionId'          => '',
        'collectionSlug'        => '',
        'collectionName'        => '',
        'collectionDescription' => '',
        'dateAdded'             => '',
    );

    protected $envelopeId;
    protected $tagId;
    public $tag;
    public $name;
    public $description;

    protected $collectionId;
    public $collectionSlug;
    public $collectionName;
    public $collectionDescription;

    public $dateAdded;
}
