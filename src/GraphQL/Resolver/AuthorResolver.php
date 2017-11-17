<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Type\Definition\ResolveInfo;

class AuthorResolver implements Resolver
{
    public function __invoke($author, $args, $context, ResolveInfo $info)
    {
        switch ($info->fieldName) {
            case 'articles':
                return DataProvider::findArticlesByAuthorId($author['id']);
            default:
                return $author[$info->fieldName];
        }
    }
}