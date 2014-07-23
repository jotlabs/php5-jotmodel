<?php
namespace JotModelExamples\SqlSaver;

use JotModel\Queries\QueryBuilder;
use JotModel\Queries\Sql\SqlContentSaver;
use JotModel\Queries\Sql\Statements\InsertStatement;

class VideoSqlSaver extends SqlContentSaver
{
    protected $queries = array(
        'saveVideo'      => 'INSERT INTO `videos`  VALUES(NULL, :sourceId, :sourceUrl, :posterName, :posterProfile, :datePosted, :duration, :numberViews);',
        'saveVideoBlob'  => 'INSERT INTO `video_blobs` VALUES()',
        'saveVideoImage' => 'INSERT INTO `video_blobs` VALUES()'
    );


    public function save($video)
    {
        $response = false;
        $dbVideo  = $this->getVideoBySourceId($video->sourceId);

        if ($dbVideo) {
            // Existing video, it's an update
            echo "[U]";
        } else {
            // New video, it's an insert
            echo "[I]";
            $videoId = $this->saveVideo($video);

            if ($videoId) {
                //echo "[-INFO-] Video saved. Video id: {$videoId}\n";
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

        $params = array(
            ':sourceId'      => $video->sourceId,
            ':sourceUrl'     => $video->sourceUrl,
            ':posterName'    => $video->posterName,
            ':posterProfile' => $video->posterProfile,
            ':datePosted'    => $video->datePosted,
            ':duration'      => $video->duration,
            ':numberViews'   => $video->numberViews
        );

        $response = $this->dataSource->insert($insert, $params);

        if ($response) {
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
}
