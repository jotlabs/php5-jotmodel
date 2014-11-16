<?php
namespace JotModel\Repositories;

use JotModel\Queries\QueryBuilder;

class ContentRepository
{
    protected $dataSource;


    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }


    public function getBySlug($slug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);

        $query   = $builder->build();
        $results = $this->dataSource->findOne($query);

        return $results;
    }


    public function getRecentContent($start = 0, $length = 5)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->setQueryName('getRange')
            ->setRange($start, $length)
            ->filter('statusId', 1)
            ->setSort('dateAdded', false);

        $query    = $builder->build();
        $content = $this->dataSource->find($query);

        return $content;
    }


    public function getContentByCategory($category, $start = 0, $length = 5)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->setQueryName('getByCategory')
            ->filter('category', $category)
            ->setRange($start, $length)
            ->setSort('dateAdded', false);

        $query   = $builder->build();
        $content = $this->dataSource->find($query);

        return $content;
    }
}
