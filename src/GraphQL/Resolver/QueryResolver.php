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
        if ('authors' === $info->fieldName) {
            return $this->dataProvider->allAuthors();
        }

        if ('articles' === $info->fieldName) {
            return $this->dataProvider->allArticles();
        }

        return null;
    }
}