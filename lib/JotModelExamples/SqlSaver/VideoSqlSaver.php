<?php
namespace JotModelExamples\SqlSaver;

use JotModel\Queries\QueryBuilder;
use JotModel\Queries\Sql\SqlSaver;
use JotModel\Queries\Sql\Statements\InsertStatement;

class VideoSqlSaver extends SqlSaver
{
    protected $dataSource;
    protected $videoId;

    protected $queries = array(
        'saveVideo'      => 'INSERT INTO `videos`  VALUES(NULL, :sourceId, :sourceUrl, :posterName, :posterProfile, :datePosted, :duration, :numberViews);',
        'saveVideoBlob'  => 'INSERT INTO `video_blobs` VALUES()',
        'saveVideoImage' => 'INSERT INTO `video_blobs` VALUES()',
        'saveEnvelope'   => 'INSERT INTO `content` VALUES(NULL, :statusId, :modelId, :contentId, :slug, :title, :excerpt, :extra1, :extra2, :permalink, :image, :dateAdded, :dateUpdated, :version, :score);'
    );

    protected $saveTree = array(
    );


    public function __construct()
    {

    }


    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /****************

    * check video slug doesn't already exist
    * Save video
    * Get video id
        * Save video blobs
        * Save video images
        * Save content envelope

    ****************/

    public function save($video)
    {
        $response = false;
        $dbVideo  = $this->getVideoBySourceId($video->sourceId);

        if ($dbVideo) {
            // Existing video, it's an update
            echo "[UPDATE]";
        } else {
            // New video, it's an insert
            echo "[INSERT]";
            $videoId = $this->saveVideo($video);

            if ($videoId) {
                echo "[-INFO-] Video saved. Video id: {$videoId}\n";
                //$this->saveVideoBlobs($video->blobs, $videoId);
                //$this->saveVideoImages($video->blobs, $videoId);
                $response = $this->saveEnvelope($video, $videoId);
            }
        }

        return $response;
    }


    protected function getVideoBySourceId($sourceId)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getBySourceId')
            ->filter('sourceId', $sourceId);

        $query = $builder->build();
        $video = $this->dataSource->findOne($query, false);

        return $video;
    }


    protected function saveVideo($video)
    {
        $videoId = null;
        $stmName = 'saveVideo';
        $insert = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->queries[$stmName]);


        //print_r($video);
        //print_r($insert);

        // How do we get the parameters needed?
        $params = array(
            ':sourceId'      => $video->sourceId,
            ':sourceUrl'     => $video->sourceUrl,
            ':posterName'    => $video->posterName,
            ':posterProfile' => $video->posterProfile,
            ':datePosted'    => $video->datePosted,
            ':duration'      => $video->duration,
            ':numberViews'   => $video->numberViews
        );

        //print_r($params);
        $response = $this->dataSource->insert($insert, $params);

        if ($response) {
            //echo "[-INFO-] Video saved.\n";

            // Get the video id of the newly saved video and return it.
            $dbVideo  = $this->getVideoBySourceId($video->sourceId);

            if ($dbVideo) {
                $videoId = $dbVideo->getId();
            }
        }

        return $videoId;
    }


    protected function saveVideoBlobs($blobs, $videoId)
    {

    }


    protected function saveVideoImages($images, $videoId)
    {

    }


    protected function saveEnvelope($model, $contentId)
    {
        print_r($model);

        $stmName = 'saveEnvelope';
        $insert = new InsertStatement();
        $insert->setQueryName($stmName);
        $insert->setStatement($this->queries['saveEnvelope']);

        $statusId = 1;
        $modelId  = 1;

        // How do we get the parameters needed?
        $params = array(
            ':statusId'    => $statusId,
            ':modelId'     => $modelId,
            ':contentId'   => $contentId,
            ':slug'        => $model->slug,
            ':title'       => $model->title,
            ':excerpt'     => $model->excerpt,
            ':extra1'      => $model->extra1,
            ':extra2'      => $model->extra2,
            ':permalink'   => $model->permalink,
            ':image'       => $model->imageTemplate,
            ':dateAdded'   => $model->dateAdded,
            ':dateUpdated' => $model->dateUpdated,
            ':version'     => $model->version,
            ':score'       => $model->score
        );

        //print_r($params);
        $response = $this->dataSource->insert($insert, $params);
        return $response;
    }
}
