<?php
namespace JotBooks\Models;

class Book
{
    public static $MODEL='book';
    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'getBySlug' => 'SELECT {fieldList} from `books` WHERE slug = :slug'
        ),
        'hydrate' => array(),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'id'          => '',
        'title'       => '',
        'subtitle'    => '',
        'slug'        => '',
        'isbn'        => '',
        'publishDate' => '',
        'publisher'   => '@Publisher',
        'authors'     => '@Author'
    );


    protected $id;

    public $title;
    public $subtitle;
    public $slug;

    public $isbn;
    public $publishDate;

    public $publisher;
    public $authors;
}
