<?php declare(strict_types=1);


namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Type\Definition\ResolveInfo;

class QueryResolver implements Resolver
{
    public function __invoke($query, $args, $context, ResolveInfo $info)
    {
        switch($info->fieldName) {
            case 'authors':
                return DataProvider::allAuthors();
            case 'articles':
                return DataProvider::allArticles();
            default:
                return null;
        }
    }
}