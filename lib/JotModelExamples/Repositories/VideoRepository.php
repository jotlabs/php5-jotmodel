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