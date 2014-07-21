<?php
namespace JotModel\Queries\Sql;

use PHPUnit_Framework_TestCase;
use JotModelExamples\Models\Video;

class SqlSaverTest extends PHPUnit_Framework_TestCase
{
    protected $saver;


    public function setUp()
    {
        $this->saver = new SqlSaver();
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModel\Queries\Sql\SqlSaver'));
        $this->assertNotNull($this->saver);
        $this->assertTrue(is_a($this->saver, 'JotModel\Queries\Sql\SqlSaver'));
    }


    public function testModelSaver()
    {
        $model = $this->createVideoModel();
        $this->saver->setModel($model);

        
    }


    protected function createVideoModel()
    {
        $video = new Video();

        // Standard envelope
        $video->slug    = 'unit-test-fixture';
        $video->title   = 'Unit test Fixture';
        $video->excerpt = 'This is an excerpt of a unit test fixture';

        $video->permalink     = 'http://example.com/unit/test.fixture';
        $video->imageTemplate = 'IMAGE';

        //$video->status;
        //$video->model;
        //$video->extra1;
        //$video->extra2;
        //$video->dateAdded;
        //$video->dateUpdated;
        //$video->version;
        //$video->score;

        // Video specific
        $video->sourceId      = 'unit-test.fixture';
        $video->sourceUrl     = 'http://example.com/unit/test.fixture';
        $video->posterName    = 'Uncle Nit';
        $video->posterProfile = 'http://example.com/profile/unit';
        $video->datePosted    = '2014-07-15T06:46:00';

        $video->duration      = 181;
        $video->numberViews   = 8192;

        print_r($video);
        return $video;
    }
}
