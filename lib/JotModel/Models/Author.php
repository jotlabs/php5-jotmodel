<?php
namespace JotModel\Models;

class Author
{
    public static $MODEL = 'content_authors';

    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'authorBySlug' => 'SELECT * FROM `content_authors` WHERE slug = :slug;'
        ),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'id'        => '',
        'name'      => '',
        'slug'      => '',
        'shortBio'  => '',
        'image'     => '',
        'aboutSlug' => '',
        'bio'       => ''
    );

    protected $id;
    public $name;
    public $slug;
    public $shortBio;
    public $image;
    public $aboutSlug;
    public $bio;


    public function getId()
    {
        return $this->id;
    }
}
