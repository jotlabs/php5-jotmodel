<?php
namespace JotModelExamples\Repositories;

use JotModel\Queries\QueryBuilder;

class VideoRepository
{
    protected $dataSource;


    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }


    public function getVideos($start = 0, $length = 10)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getRange')
            ->setRange($start, $length)
            ->setSort('score', false);

        $query   = $builder->build();
        $results = $this->dataSource->find($query);

        return $results;
    }


    public function getVideosByTag($tag)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getByTag')
            ->filter('tag', $tag);

        $query   = $builder->build();
        $results = $this->dataSource->find($query);

        return $results;
    }


    public function getVideosByCategory($category)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getByCategory')
            ->filter('category', $category);

        $query   = $builder->build();
        $results = $this->dataSource->find($query);

        return $results;
    }


    public function getBySlug($slug)
    {
        $builder = new QueryBuilder();
        $builder
            ->setModelClass('JotModelExamples\Models\Video')
            ->setQueryName('getBySlug')
            ->filter('slug', $slug);

        $query   = $builder->build();
        $results = $this->dataSource->findOne($query);

        return $results;
    }
}
