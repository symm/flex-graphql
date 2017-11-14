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
                    case 'sum':
                        return $args['x'] * $args['y'];
                    default:
                        return null;
                }
            };
        }

        if ($name === 'Calc') {
            $typeConfig['resolveField'] = function($root, $args, $context, ResolveInfo $info) {
                switch($info->fieldName) {
                    case 'sum':
                        return $args['x'] + $args['y'];
                    default:
                        return null;
                }
            };
        }

        return $typeConfig;
    }
}