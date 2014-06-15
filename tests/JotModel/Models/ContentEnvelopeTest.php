<?php
namespace JotModel\Models;

use PHPUnit_Framework_TestCase;

class ContentEnvelopeTestModel extends ContentEnvelope
{
    public $testField1 = 'testValue';
    public $testField2 = 2;
}


class ContentEnvelopeTest extends PHPUnit_Framework_TestCase
{
    protected $model;


    public function setUp()
    {
        $this->model = new ContentEnvelopeTestModel();
    }


    public function testClassExists()
    {
        $this->assertTrue(class_exists('JotModel\Models\ContentEnvelope'));
        $this->assertNotNull($this->model);
        $this->assertTrue(is_a($this->model, 'JotModel\Models\ContentEnvelope'));
        $this->assertTrue(is_subclass_of(
            'JotModel\Models\ContentEnvelopeTestModel',
            'JotModel\Models\ContentEnvelope'
        ));
    }


    public function testEnvelopeValuesAreRetrievable()
    {
        $this->assertTrue(property_exists($this->model, 'envelopeId'));
    }
}
