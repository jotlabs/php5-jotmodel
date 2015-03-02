<?php
namespace JotModel\Models;

class ContentEnvelope
{
    # TODO: Abstract these static properties into a schema class or config object
    public static $MODEL = 'content';

    public static $DECORATORS = array('tags', 'categories');

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'getBySlug' => 'SELECT {fieldList} FROM `content_envelope` AS `ce` WHERE slug = :slug;',
            'getByTag'  => 'SELECT {fieldList} FROM `content_envelope` AS `ce` WHERE tag = :tag;'
        ),
        'hydrate' => array(
            // Content decorators
            'tags' => array(
                'modelClass' => 'JotModel\Models\Tag',
                'tableName'  => 'tagged_content',
                'where'      => array('envelopeId' => 'envelopeId'),
                'properties' => array('envelopeId' => 'envelopeId')
            ),
            'categories' => array(
                'modelClass' => 'JotModel\Models\Category',
                'tableName'  => 'category_content',
                'where'      => array('envelopeId' => 'envelopeId'),
                'properties' => array('envelopeId' => 'envelopeId')
            )
        ),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'envelopeId'    => 'ce.envelopeId',
        'contentId'     => 'ce.contentId',
        'status'        => 'ce.status',
        'type'          => 'ce.type',
        'model'         => 'ce.model',
        'slug'          => 'ce.slug',
        'title'         => 'ce.title',
        'excerpt'       => 'ce.excerpt',
        'extra1'        => 'ce.extra1',
        'extra2'        => 'ce.extra2',
        'pageUrl'       => 'ce.pageUrl',
        'permalink'     => 'ce.permalink',
        'imageTemplate' => 'ce.imageTemplate',
        'dateAdded'     => 'ce.dateAdded',
        'dateUpdated'   => 'ce.dateUpdated',
        'guid'          => 'ce.guid',
        'version'       => 'ce.version',
        'score'         => 'ce.score',

        'authorId'        => 'ce.authorId',
        'authorName'      => 'ce.authorName',
        'authorSlug'      => 'ce.authorSlug',
        'authorImage'     => 'ce.authorImage',
        'authorShortBio'  => 'ce.authorShortBio',
        'authorBio'       => 'ce.authorBio',
        'authorAboutSlug' => 'ce.authorAboutSlug'
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

    public $guid;
    public $version;
    public $score;

    public $authorId;
    public $authorName;
    public $authorSlug;
    public $authorImage;
    public $authorShortBio;
    public $authorBio;
    public $authorAboutSlug;


    public function getEnvelopeId()
    {
        return $this->envelopeId;
    }
}
