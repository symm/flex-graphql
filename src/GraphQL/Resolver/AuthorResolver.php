<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
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
        switch ($info->fieldName) {
            case 'articles':
                return $this->dataProvider->findArticlesByAuthorId($author['id']);
            default:
                return $author[$info->fieldName];
        }
    }
}