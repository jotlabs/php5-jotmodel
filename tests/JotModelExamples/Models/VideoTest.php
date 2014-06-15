<?php
namespace JotModelExamples\Models;

use JotModelExamples\Models\Video;
use PHPUnit_Framework_TestCase;

class VideoTest extends PHPUnit_Framework_TestCase
{
    protected $video;


    public function setUp()
    {
        $this->video = new Video();
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModelExamples\Models\Video'));
        $this->assertNotNull($this->video);
        $this->assertTrue(is_a($this->video, 'JotModelExamples\Models\Video'));
        $this->assertTrue(is_a($this->video, 'JotModel\Models\ContentEnvelope'));
    }


    public function testVideoPropertiesExists()
    {
        $this->assertTrue(property_exists($this->video, 'sourceId'));
        $this->assertTrue(property_exists($this->video, 'sourceUrl'));
    }


    public function testEnvelopePropertiesExists()
    {
        $this->assertTrue(property_exists($this->video, 'slug'));
        $this->assertTrue(property_exists($this->video, 'title'));
    }
}
