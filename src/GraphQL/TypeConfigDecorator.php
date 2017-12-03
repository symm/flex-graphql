<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Resolver\ArticleResolver;
use App\GraphQL\Resolver\AuthorResolver;
use App\GraphQL\Resolver\MutationResolver;
use App\GraphQL\Resolver\QueryResolver;
use Psr\Container\ContainerInterface;

class TypeConfigDecorator
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($typeConfig, $typeDefinitionNode)
    {
        $resolverMap = [
            'Query' => QueryResolver::class,
            'Article' => ArticleResolver::class,
            'Author' => AuthorResolver::class,
            'Mutation' => MutationResolver::class,
        ];

        if (array_key_exists($typeConfig['name'], $resolverMap)) {
            $typeConfig['resolveField'] = $this->container->get($resolverMap[$typeConfig['name']]);
        }

        return $typeConfig;
    }
}
