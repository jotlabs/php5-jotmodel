<?php
namespace JotModel\DataSources;

use JotModel\Queries\QueryBuilder;
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
     */
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


    protected function createDb()
    {
        $db = new PDO(self::$dsn);

        $sql = file_get_contents(PROJECT_ROOT . self::$schemaFile);
        $db->exec($sql);

        $sql = file_get_contents(PROJECT_ROOT . self::$dataFile);
        $db->exec($sql);

        return $db;
    }
}
