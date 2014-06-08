<?php
namespace JotModel\Models;

class ContentEnvelope
{
    # TODO: Abstract these static properties into a schema class or config object
    public static $MODEL = 'content';

    public static $SQL_QUERIES = array(
        'getBySlug' => 'SELECT {fieldList} FROM `content_envelope` WHERE slug=:slug;'
    );

    public static $SQL_FRAGMENTS = array(
        'joins' => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'    => '_envelope_id',
        'contentId'     => '_content_id',
        'status'        => 'status',
        'model'         => 'model',
        'slug'          => 'slug',
        'title'         => 'title',
        'excerpt'       => 'excerpt',
        'permalink'     => 'permalink',
        'imageTemplate' => 'imageTemplate',
        'dateAdded'     => 'dateAdded',
        'dateUpdated'   => 'dateUpdated'
    );


    protected $envelopeId;
    protected $contentId;

    public $status;
    public $model;

    public $slug;
    public $title;
    public $excerpt;

    public $permalink;
    public $imageTemplate;

    public $dateAdded;
    public $dateUpdated;
}
