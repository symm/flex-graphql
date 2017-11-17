<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;

class AuthorResolver implements Resolver
{
    /** @var DataProvider */
    private $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function __invoke($author, $args, $context, ResolveInfo $info)
    {
        if ('articles' === $info->fieldName) {
            return $this->dataProvider->findArticlesByAuthorId($author['id']);
        }

        return Executor::defaultFieldResolver($author, $args, $context, $info);
    }
}