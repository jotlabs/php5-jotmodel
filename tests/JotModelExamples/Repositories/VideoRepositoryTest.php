<?php
namespace JotModelExamples\Repositories;

use PDO;
use PHPUnit_Framework_TestCase;
use JotModel\DataSources\PdoDataSource;
use JotModelExamples\Repositories\VideoRepository;

class VideoRepositoryTest extends PHPUnit_Framework_TestCase
{
    protected $repo;


    public function setUp()
    {
        $db = new PDO('sqlite:tests/data/unittest.db');
        $dataSource = new PdoDataSource($db);
        $this->repo = new VideoRepository($dataSource);
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModelExamples\Repositories\VideoRepository'));
        $this->assertNotNull($this->repo);
        $this->assertTrue(is_a($this->repo, 'JotModelExamples\Repositories\VideoRepository'));
    }


    public function testGetBuSlugReturnsContentEnvelope()
    {
        $slug = 'the-ashtanga-primary-series';

        $video = $this->repo->getBySlug($slug);
        $this->assertNotNull($video);
        //print_r($video);

        $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));
        $this->assertTrue(is_a($video, 'JotModel\Models\ContentEnvelope'));

        $this->assertEquals($slug, $video->slug);
        $this->assertEquals('video', $video->model);
        $this->assertEquals('video', $video->type);
        $this->assertEquals('rAaHx5qsqhY', $video->sourceId);
    }
}