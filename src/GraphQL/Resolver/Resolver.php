<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

interface Resolver
{
    public function __invoke($root, $args, $context, ResolveInfo $info);
}