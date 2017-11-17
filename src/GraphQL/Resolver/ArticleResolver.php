<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;

class ArticleResolver implements Resolver
{
    /** @var DataProvider */
    private $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function __invoke($article, $args, $context, ResolveInfo $info)
    {
        if ('author' === $info->fieldName) {
            return $this->dataProvider->findAuthorById($article['authorId']);
        }

        return Executor::defaultFieldResolver($article, $args, $context, $info);
    }
}