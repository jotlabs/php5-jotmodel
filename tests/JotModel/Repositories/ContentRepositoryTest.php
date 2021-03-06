<?php
namespace JotModel\Repositories;

use PHPUnit_Framework_TestCase;
use PDO;
use JotModel\DataSources\PdoDataSource;

class ContentRepositoryTest extends PHPUnit_Framework_TestCase
{
    protected $repo;


    public function setUp()
    {
        $db = new PDO('sqlite:tests/data/unittest.db');
        $dataSource = new PdoDataSource($db);
        $this->repo = new ContentRepository($dataSource);
    }


    public function testContentRepositoryExists()
    {
        $this->assertTrue(class_exists('JotModel\Repositories\ContentRepository'));
        $this->assertNotNull($this->repo);
        $this->assertTrue(is_a($this->repo, 'JotModel\Repositories\ContentRepository'));
    }


    public function testGetCategoryReturnsCategory()
    {
        $slug = 'ashtanga';
        $category = $this->repo->getCategory($slug);

        $this->assertNotNull($category);
        $this->assertTrue(is_a($category, 'JotModel\Models\Category'));

        $this->assertEquals($slug, $category->slug);
        $this->assertEquals('Ashtanga', $category->name);
    }


    public function testGetBySlugReturnsContentModel()
    {
        $slug = 'the-ashtanga-primary-series';
        $content = $this->repo->getBySlug($slug);
        //print_r($content);
        $this->assertNotNull($content);
        $this->assertNotNull($content->slug);
        $this->assertEquals($slug, $content->slug);

        $this->assertTrue(is_a($content, 'JotModel\Models\ContentEnvelope'));
        $this->assertEquals($slug, $content->slug);
        $this->assertEquals('video', $content->model);
        $this->assertEquals('A', $content->status);

        $this->assertEquals(1, $content->version);
        $this->assertNotNull($content->pageUrl);
        $this->assertNotNull($content->permalink);

        // Test author fields are correct
        $this->assertEquals(2, $content->authorId);
        $this->assertEquals('ashtanga-yogi', $content->authorSlug);
        $this->assertEquals('/about/ashtanga-yogi', $content->authorAboutSlug);
    }


    public function testGetRecentContentReturnsItems()
    {
        $content = $this->repo->getRecentContent();

        $this->assertNotNull($content);
        $this->assertEquals(5, count($content));

        foreach ($content as $item) {
            $this->assertTrue(is_a($item, 'JotModel\Models\ContentEnvelope'));
        }
    }


    public function testGetContentByCategoryReturnsItems()
    {
        $content = $this->repo->getContentByCategory('ashtanga');

        $this->assertNotNull($content);
        $this->assertEquals(5, count($content));

        foreach ($content as $item) {
            $this->assertTrue(is_a($item, 'JotModel\Models\ContentEnvelope'));
        }
    }
}
