<?php
namespace JotModel\Models;

class ContentModel
{
    public static $MODEL = 'content_type_models';
    public static $SQL_FIELDS = array(
        'id'        => '',
        'slug'      => '',
        'title'     => '',
        'typeId'    => '',
        'typeSlug'  => '',
        'typeTitle' => ''
    );
    public static $SQL_FRAGMENTS = array();


    protected $id;
    public $slug;
    public $title;

    protected $typeId;
    public $typeSlug;
    public $typeTitle;


    public function getId()
    {
        return $this->id;
    }
}
