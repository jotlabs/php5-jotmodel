<?php
namespace JotBooks\Repositories;

use PHPUnit_Framework_TestCase;
use PDO;
use JotModel\DataSources\PdoDataSource;

class BookRepositoryTest extends PHPUnit_Framework_TestCase
{
    protected $repo;


    public function setUp()
    {
        $db = new PDO('sqlite:tests/data/jotbooks.db');
        $dataSource = new PdoDataSource($db);
        $this->repo = new BookRepository($dataSource);
    }


    public function testContentRepositoryExists()
    {
        $this->assertTrue(class_exists('JotBooks\Repositories\BookRepository'));
        $this->assertNotNull($this->repo);
        $this->assertTrue(is_a($this->repo, 'JotBooks\Repositories\BookRepository'));
    }


    public function testGetBySlugReturnsContentModel()
    {
        $slug = 'chess-secrets-great-attackers';
        $book = $this->repo->getBySlug($slug);
        //print_r($book);
        $this->assertNotNull($book);
        $this->assertNotNull($book->slug);
        $this->assertEquals($slug, $book->slug);

        $this->assertTrue(is_a($book, 'JotBooks\Models\Book'));

        //print_r($book);
    }
}
