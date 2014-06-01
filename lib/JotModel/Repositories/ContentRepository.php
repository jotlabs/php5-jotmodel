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
            ->setModel('content')
            ->setModelClass('JotModel\Models\ContentEnvelope')
            ->filter('slug', $slug);

        $query   = $builder->build();
        $results = $this->dataSource->findOne($query);

        return $results;
    }
}
