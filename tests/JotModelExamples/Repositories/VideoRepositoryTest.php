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


    public function testGetVideosReturnsArrayOfVideos()
    {
        $videos = $this->repo->getVideos();

        //echo "Videos: ";
        //print_r($videos);

        $this->assertNotNull($videos);
        $this->assertTrue(is_array($videos));
        $this->assertEquals(10, count($videos));

        $lastScore = false;
        foreach ($videos as $video) {
            $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));

            // Test the score order is descending
            if ($lastScore !== false) {
                $this->assertTrue($lastScore >= $video->score);
            }

            $lastScore = $video->score;
        }
    }


    public function testGetVideosPageTwoReturnsPageTwoVideos()
    {
        $videos = $this->repo->getVideos(10, 10);
        $this->assertNotNull($videos);
        $this->assertTrue(is_array($videos));
        $this->assertEquals(6, count($videos));
    }


    public function testCallingGetVideosForTwoDifferentPagesReturnsEachPage()
    {
        $videos1 = $this->repo->getVideos();
        $videos2 = $this->repo->getVideos(10, 10);

        $this->assertEquals(10, count($videos1));
        $this->assertEquals(6, count($videos2));
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
        $this->assertEquals(1, $video->version);

        // Check we have tags
        $this->assertTrue(!empty($video->tags));
        $this->assertNotNull($video->tags);

        $this->assertTrue(is_array($video->tags));
        $this->assertEquals(1, count($video->tags));
        $tag = $video->tags[0];
        $this->assertNotNull($tag);
        $this->assertTrue(is_a($tag, 'JotModel\Models\Tag'));


        // Check we have categories
        $this->assertTrue(!empty($video->categories));
        $this->assertNotNull($video->categories);

        $this->assertTrue(is_array($video->categories));
        $this->assertEquals(1, count($video->categories));
        $category = $video->categories[0];
        $this->assertNotNull($category);
        $this->assertTrue(is_a($category, 'JotModel\Models\Category'));
        $this->assertNotNull($category->collectionWeight);
    }


    public function testGetVideosByTagReturnsArrayOfVideos()
    {
        $videos = $this->repo->getVideosByTag('kino-macgregor');

        //print_r($videos);

        $this->assertNotNull($videos);
        $this->assertTrue(is_array($videos));
        $this->assertEquals(3, count($videos));

        foreach ($videos as $video) {
            $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));
        }
    }


    public function testGetVideosByCategoryReturnsArrayOfVideos()
    {
        $videos = $this->repo->getVideosByCategory('ashtanga');

        //print_r($videos);

        $this->assertNotNull($videos);
        $this->assertTrue(is_array($videos));
        $this->assertEquals(16, count($videos));

        foreach ($videos as $video) {
            $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));
        }
    }
}
