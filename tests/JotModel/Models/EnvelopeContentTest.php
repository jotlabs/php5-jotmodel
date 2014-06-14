<?php
namespace JotModel\Models;

use PHPUnit_Framework_TestCase;

class TestModel extends EnvelopeContent
{
    public $slug = 'testModelSlug';
}

class EnvelopeContentTest extends PHPUnit_Framework_TestCase
{
    protected $model;


    public function setUp()
    {
        $this->model = new TestModel();

        $envelope = new ContentEnvelope();
        $envelope->slug = 'envelopeSlug';
        $envelope->title = 'envelopeTitle';

        $this->model->setEnvelope($envelope);
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModel\Models\EnvelopeContent'));
        $this->assertNotNull($this->model);
        $this->assertTrue(is_a($this->model, 'JotModel\Models\EnvelopeContent'));
    }


    public function testHasEnvelopeReturnsTrue()
    {
        $this->assertTrue($this->model->hasEnvelope());
    }


    public function testGetEnvelopeOverlaidPropertyiesReturnsModelPropertyValue()
    {
        $this->assertEquals('testModelSlug', $this->model->slug);
        $this->assertEquals('testModelSlug', $this->model->getSlug());
    }


    public function testGetEnvelopePropertyReturnsEnvelopePropertyValue()
    {
        $this->assertEquals('envelopeTitle', $this->model->getTitle());
    }
}
