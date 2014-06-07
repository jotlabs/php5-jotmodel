<?php
namespace JotModel\Models;

class ContentEnvelope
{
    public static $MODEL = 'content';

    public static $SQL_QUERIES = array(
        'getBySlug' => 'SELECT {fieldList} FROM `content` {joins} WHERE content.slug=:slug;'
    );

    public static $SQL_FRAGMENTS = array(
        'joins' => array(
            'LEFT JOIN `content_status` ON content._status_id = content_status._id',
            'LEFT JOIN `content_models` ON content._model_id = content_models._id',
            'LEFT JOIN `content_types`  ON content_models._type_id = content_types._id'
        )
    );

    public static $SQL_FIELDS = array(
        'envelopeId'    => 'content._id',
        'contentId'     => 'content._content_id',
        'status'        => 'content_status.slug',
        'model'         => 'content_models.slug',
        'slug'          => 'content.slug',
        'title'         => 'content.title',
        'excerpt'       => 'content.excerpt',
        'permalink'     => 'content.permalink',
        'imageTemplate' => 'content.imageTemplate',
        'dateAdded'     => 'content.dateAdded',
        'dateUpdated'   => 'content.dateUpdated'
    );


    protected $envelopeId;
    protected $contentId;

    protected $status;
    protected $model;

    public $slug;
    public $title;
    public $excerpt;

    public $permalink;
    public $imageTemplate;

    public $dateAdded;
    public $dateUpdated;
}
