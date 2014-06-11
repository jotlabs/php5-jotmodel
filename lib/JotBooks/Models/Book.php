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
                'join'  => 'JOIN `book_authors` ON book_authors.authorId = authors.id',
                'where' => array('book_authors.bookId' =>  'bookId'),
                'properties' => array('bookId' => 'id')
            ),
            'publishers' => array(
                'modelClass' => 'JotBooks\Models\Publisher',
                'join'       => 'JOIN `book_publishers` ON book_publishers.publisherId = publishers.id',
                'where'      => array('book_publishers.bookId' => 'bookId'),
                'properties' => array('bookId' => 'id')
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
        'publishers'  => '@publishers',
        'authors'     => '@authors'
    );


    protected $id;

    public $title;
    public $subtitle;
    public $slug;

    public $isbn;
    public $publishDate;

    public $publishers;
    public $authors;


    public function getId()
    {
        return $this->id;
    }
}
