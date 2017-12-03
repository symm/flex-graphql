<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataLoader\BatchAuthorLoader;
use App\Repository\AuthorRepository;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;

class ArticleResolver implements Resolver
{
    private $authorLoader;

    public function __construct(AuthorRepository $authorRepository)
    {
        // TODO: inject from service container
        $this->authorLoader = BatchAuthorLoader::factory(Executor::getPromiseAdapter(), $authorRepository);
    }

    public function __invoke($article, $args, $context, ResolveInfo $info)
    {
        if ('author' === $info->fieldName) {
            return $this->authorLoader->load($article['authorId']);
        }

        return Executor::defaultFieldResolver($article, $args, $context, $info);
    }
}
