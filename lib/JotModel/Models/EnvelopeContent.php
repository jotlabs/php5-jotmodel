<?php
namespace JotModel\Models;

abstract class EnvelopeContent
{
    protected $envelope;


    public function setEnvelope($envelope)
    {
        $this->envelope = $envelope;
    }


    public function getEnvelope()
    {
        return $this->envelope;
    }


    public function hasEnvelope()
    {
        return !empty($this->envelope);
    }


    public function getSlug()
    {
        return $this->getOverlaidProperty('slug');
    }

    public function getTitle()
    {
        return $this->getOverlaidProperty('title');
    }


    protected function getOverlaidProperty($propName)
    {
        if (property_exists($this, $propName)) {
            return $this->{$propName};
        } elseif (property_exists($this->envelope, $propName)) {
            return $this->envelope->{$propName};
        }
    }
}
