<?php
namespace JotModel\Models;

class ContentEnvelope
{
    # TODO: Abstract these static properties into a schema class or config object
    public static $MODEL = 'content';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'getBySlug' => 'SELECT {fieldList} FROM `content_envelope` WHERE slug=:slug;'
        ),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'    => 'envelopeId',
        'contentId'     => 'contentId',
        'status'        => 'status',
        'type'          => 'type',
        'model'         => 'model',
        'slug'          => 'slug',
        'title'         => 'title',
        'excerpt'       => 'excerpt',
        'extra1'        => 'extra1',
        'extra2'        => 'extra2',
        'permalink'     => 'permalink',
        'imageTemplate' => 'imageTemplate',
        'dateAdded'     => 'dateAdded',
        'dateUpdated'   => 'dateUpdated',
        'version'       => 'version',
        'score'         => 'score'
    );


    protected $envelopeId;
    protected $contentId;

    public $status;
    public $model;

    public $slug;
    public $title;
    public $excerpt;
    public $extra1;
    public $extra2;

    public $permalink;
    public $imageTemplate;

    public $dateAdded;
    public $dateUpdated;

    public $version;
    public $score;
}
