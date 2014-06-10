<?php
namespace JotBooks\Models;

class Book
{
    public static $MODEL='book';
    public static $SQL_FRAGMENTS = array(
        'queries' => array(
            'getBySlug' => 'SELECT {fieldList} from `books` WHERE slug = :slug'
        ),
        'hydrate' => array(
            'authors' => array(
                'modelClass' => 'JotBooks\Models\Author',
                'paramField' => 'bookId',
                'paramValue' => 'id',
                'join'  => 'JOIN `book_authors` ON book_authors.authorId = authors.id',
                'where' => array('book_authors.bookId' =>  'bookId')
            ),
            'publishers' => array(
                'modelClass' => 'JotBooks\Models\Publisher',
                'paramField' => 'bookId',
                'paramValue' => 'id',
                'join'       => 'JOIN `publishers` ON book_publishers.publisherId = publishers.id',
                'where'      => array('book_publishers.bookId' => 'bookId')
            )
        ),
        'joins'   => array()
    );

    public static $SQL_FIELDS = array(
        'id'          => '',
        'title'       => '',
        'subtitle'    => '',
        'slug'        => '',
        'isbn'        => '',
        'publishDate' => '',
        'publisher'   => '@publishers',
        'authors'     => '@authors'
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
