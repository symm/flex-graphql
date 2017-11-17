<?php declare(strict_types=1);


namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Type\Definition\ResolveInfo;

class QueryResolver implements Resolver
{
    /** @var DataProvider */
    private $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function __invoke($query, $args, $context, ResolveInfo $info)
    {
        switch($info->fieldName) {
            case 'authors':
                return $this->dataProvider->allAuthors();
            case 'articles':
                return $this->dataProvider->allArticles();
            default:
                return null;
        }
    }
}