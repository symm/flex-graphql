<?php declare(strict_types=1);

namespace App\GraphQL;


use GraphQL\Type\Definition\ResolveInfo;

class TypeConfigDecorator
{
    public function __invoke($typeConfig, $typeDefinitionNode)
    {
        $name = $typeConfig['name'];

        if ($name === 'Query') {
            $typeConfig['resolveField'] = function($root, $args, $context, ResolveInfo $info) {
                switch($info->fieldName) {
                    case 'echo':
                        return $args['message'];
                    default:
                        return null;
                }
            };
        }

        return $typeConfig;
    }
}