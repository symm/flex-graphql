<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Type\Definition\ResolveInfo;

class ArticleResolver implements Resolver
{
    public function __invoke($article, $args, $context, ResolveInfo $info) {
            switch ($info->fieldName) {
                case 'author':
                    return DataProvider::findAuthorById($article['authorId']);
                default:
                    return $article[$info->fieldName];
            }
    }
}