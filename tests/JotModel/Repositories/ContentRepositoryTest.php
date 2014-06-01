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


    public function testGetBySlugReturnsContentModel()
    {
        $slug = 'the-ashtanga-primary-series';
        $content = $this->repo->getBySlug($slug);
        print_r($content);
        $this->assertNotNull($content);
        $this->assertNotNull($content->slug);
        $this->assertEquals($slug, $content->slug);

        $this->assertTrue(is_a($content, 'JotModel\Models\ContentEnvelope'));
    }
}
