<?php
namespace JotModel\Models;

class Tag
{
    public static $MODEL = 'tags';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'     => '',
        'tagId'          => '',
        'tag'            => '',
        'name'           => '',
        'collectionId'   => '',
        'collectionSlug' => '',
        'collectionName' => '',
        'dateAdded'      => '',
    );

    protected $envelopeId;
    protected $tagId;
    public $tag;
    public $name;

    protected $collectionId;
    public $collectionSlug;
    public $collectionName;

    public $dateAdded;
}
