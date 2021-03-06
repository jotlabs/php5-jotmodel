<?php
namespace JotModel\DataSources;

use JotModel\Queries\QueryBuilder;
use JotModelExamples\Models\Video;
use PHPUnit_Framework_TestCase;
use PDO;

class PdoDataSourceTest extends PHPUnit_Framework_TestCase
{
    public static $dsn        = 'sqlite::memory:';
    //public static $dsn        = 'sqlite:tmp/testdb.db';
    public static $schemaFile = '/var/testdata/unittest-schema.sql';
    public static $dataFile   = '/var/testdata/unittest-data.sql';
    protected $dataSource;


    public function setUp()
    {
        $db = $this->createDb();
        $this->dataSource = new PdoDataSource($db);
    }

    public function testPdoDataSourceExists()
    {
        $this->assertTrue(class_exists('JotModel\DataSources\PdoDataSource'));
        $this->assertNotNull($this->dataSource);
        $this->assertTrue(is_a($this->dataSource, 'JotModel\DataSources\PdoDataSource'));
    }

    /**
     * @expectedException JotModel\Exceptions\JotModelException
     **/
    public function testCreateWithoutPdoThrowsException()
    {
        $db = (object) null;
        $dataSource = new PdoDataSource($db);
        $this->assertFalse(true);
    }


    public function testFindOneReturnsModel()
    {
        $slug    = 'the-ashtanga-primary-series';
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);

        $query = $builder->build();
        $video = $this->dataSource->findOne($query);

        $this->assertNotNull($video);
        $this->assertTrue(is_a($video, 'JotModel\Models\ContentEnvelope'));
        $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));
        $this->assertTrue(intval($video->getEnvelopeId()) > 0);
        $this->assertNotNull($video->pageUrl);
        $this->assertNotNull($video->permalink);
    }


    public function testGetBySlugReturnsOneContent()
    {
        $guid = 'video-7';
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getByGuid')
            ->filter('guid', $guid);

        $query = $builder->build();
        $video = $this->dataSource->findOne($query);

        $this->assertNotNull($video);
        $this->assertEquals('JoHxl2k10xw', $video->sourceId);
        $this->assertEquals('forward-jump-explained-ashtanga-yoga', $video->slug);

    }


    public function testFindReturnsArrayOfModels()
    {
        $category = 'ashtanga';
        $builder  = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getByCategory')
            ->filter('category', $category);

        $query  = $builder->build();
        $videos = $this->dataSource->find($query);

        $this->assertNotNull($videos);
        $this->assertTrue(is_array($videos));
        $this->assertTrue(count($videos) > 0);

        foreach ($videos as $video) {
            $this->assertTrue(is_a($video, 'JotModel\Models\ContentEnvelope'));
            $this->assertTrue(is_a($video, 'JotModelExamples\Models\Video'));
            $this->assertTrue(intval($video->getEnvelopeId()) > 0);
        }
    }


    public function testGetAllContentModels()
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\ContentModel')
            ->setQueryName('getAllModels');

        $query  = $builder->build();
        $models = $this->dataSource->find($query);

        $this->assertNotNull($models);

        foreach ($models as $model) {
            $this->assertNotNull($model);
            $this->assertTrue(is_a($model, 'JotModel\Models\ContentModel'));
            $this->assertNotNull($model->slug);
            $this->assertNotNull($model->typeSlug);
        }
    }


    public function testSaveModelSaves()
    {
        $video    = $this->createVideoModel();
        $response = $this->dataSource->save($video);

        $this->assertTrue($response);

        // Check the video exists
        $slug     = $video->slug;
        $builder  = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);

        $query    = $builder->build();
        $newVideo = $this->dataSource->findOne($query);

        $this->assertNotNull($newVideo);
        $this->assertEquals($video->slug, $newVideo->slug);
        $this->assertEquals('video', $newVideo->type);
        $this->assertEquals('video', $newVideo->model);
        $this->assertEquals('Z', $newVideo->status);

        $this->assertNotNull($newVideo->dateAdded);
        $this->assertNotNull($newVideo->dateUpdated);
        $this->assertNotNull($newVideo->version);
        $this->assertNotNull($newVideo->score);

        $this->assertNotNull($newVideo->pageUrl);
        $this->assertNotNull($newVideo->permalink);
        $this->assertEquals($newVideo->pageUrl, $newVideo->permalink);


        // Check author
        $this->assertEquals(3, $newVideo->authorId);
        $this->assertEquals('new-author', $newVideo->authorSlug);
        $this->assertEquals('/about/new-author', $newVideo->authorAboutSlug);

        // Check categories
        $this->assertNotNull($newVideo->categories);
        $this->assertTrue(is_array($newVideo->categories));
        $this->assertTrue(count($newVideo->categories) === 2);

        $category = $newVideo->categories[0];
        $this->assertNotNull($category);
        $this->assertTrue(is_a($category, 'JotModel\Models\Category'));
        $this->assertEquals('unit-test', $category->category);
        $this->assertEquals('N', $category->isPrimary);
        $this->assertNotNull($category->description);
        $this->assertEquals('Unit test description', $category->description);

        $this->assertNotNull($category->totalArticles);
        $this->assertEquals(0, $category->totalArticles);


        $category = $newVideo->categories[1];
        $this->assertNotNull($category);
        $this->assertTrue(is_a($category, 'JotModel\Models\Category'));
        $this->assertEquals('primary-cat', $category->category);
        $this->assertEquals('Y', $category->isPrimary);
        $this->assertNotNull($category->description);


        // Check tags
        $this->assertNotNull($newVideo->tags);
        $this->assertTrue(is_array($newVideo->tags));
        $this->assertTrue(count($newVideo->tags) === 1);

        $tag = $newVideo->tags[0];
        $this->assertNotNull($tag);
        $this->assertTrue(is_a($tag, 'JotModel\Models\Tag'));
        $this->assertEquals('fixture', $tag->tag);
        $this->assertNotNull($tag->description);
    }


    protected function createDb()
    {
        $db = new PDO(self::$dsn);

        $sql = file_get_contents(PROJECT_ROOT . self::$schemaFile);
        $db->exec($sql);

        $sql = file_get_contents(PROJECT_ROOT . self::$dataFile);
        $db->exec($sql);

        return $db;
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

        $video->status = 'Z';
        //$video->model;
        //$video->extra1;
        //$video->extra2;
        //$video->dateAdded;
        //$video->dateUpdated;

        $video->guid = 'unit-test.guid';
        //$video->version;
        //$video->score;

        // Author details
        $video->authorName = 'New author';
        $video->authorSlug = 'new-author';
        $video->authorAboutSlug = '/about/new-author';

        // Video specific
        $video->sourceId      = 'unit-test.fixture';
        $video->sourceUrl     = 'http://example.com/unit/test.fixture';
        $video->posterName    = 'Uncle Nit';
        $video->posterProfile = 'http://example.com/profile/unit';
        $video->datePosted    = '2014-07-15T06:46:00';

        $video->duration      = 181;
        $video->numberViews   = 8192;

        $video->categories    = array(
            'unit-test' => (object) array(
                'slug'  => 'unit-test',
                'title' => 'Unit Test',
                'description' => 'Unit test description'
            ),
            'primary-cat' => (object) array(
                'slug'  => 'primary-cat',
                'title' => 'Primary Category',
                'isPrimary' => true
            ),
        );

        $video->tags          = array(
            'fixture'   => 'Fixture'
        );

        //print_r($video);
        return $video;
    }
}
