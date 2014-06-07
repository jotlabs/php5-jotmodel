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
}
