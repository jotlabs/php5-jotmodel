<?php
namespace JotModelExamples\Models;

use JotModel\Models\ContentEnvelope;

class Video extends ContentEnvelope
{
    # TODO: Abstract these static properties into a schema class or config object
    public static $MODEL = 'videos';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'getBySlug' => 'SELECT {fieldList} FROM `videos` {joins} WHERE slug=:slug;'
        ),
        'joins' => array(),
        'hydrate' => array(
            //'video_blobs' => array(
            //    'modelClass' => 'LotsOfYoga\Model\Models\VideoBlob',
            //    'where'      => array('videoId' => 'videoId'),
            //    'properties' => array('videoId' => 'id')
            //),
            //'video_images' => array(
            //    'modelClass' => 'LotsOfYoga\Model\Models\VideoImage',
            //    'where'      => array('videoId' => 'videoId'),
            //    'properties' => array('videoId' => 'id')
            //)
        )
    );

    public static $SQL_FIELDS = array(
        'id'            => '',
        'sourceId'      => '',
        'sourceUrl'     => '',
        'posterName'    => '',
        'posterProfile' => '',
        'datePosted'    => '',
        'duration'      => '',
        'numberViews'   => '',

        // Sub-models
        //'blobs'         => '@video_blobs',
        //'images'        => '@video_images'
    );


    protected $id;

    public $sourceId;
    public $sourceUrl;

    public $posterName;
    public $posterProfile;
    public $datePosted;

    public $duration;
    public $numberViews;

    public $blobs;
    public $images;


    public function getId()
    {
        return $this->id;
    }
}
