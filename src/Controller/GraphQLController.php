<?php

namespace App\Controller;

use GraphQL\Error\Debug;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Server\StandardServer;

class GraphQLController extends Controller
{
    public function index(Request $httpFoundationRequest)
    {
        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema(),
            'debug'  => Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE | Debug::RETHROW_INTERNAL_EXCEPTIONS
        ]);

        $psr7Factory = new DiactorosFactory();
        $request = $psr7Factory->createRequest($httpFoundationRequest);

        $result = $server->executePsrRequest($request);

        return new Response(json_encode($result));
    }

    private function getSchema()
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => ['type' => Type::string()],
                    ],
                    'resolve' => function ($root, $args) {
                        return $root['prefix'] . $args['message'];
                    }
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Calc',
            'fields' => [
                'sum' => [
                    'type' => Type::int(),
                    'args' => [
                        'x' => ['type' => Type::int()],
                        'y' => ['type' => Type::int()],
                    ],
                    'resolve' => function ($root, $args) {
                        return $args['x'] + $args['y'];
                    },
                ],
            ],
        ]);

        // See docs on schema options:
        // http://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
        $schema = new Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
        ]);


        return $schema;
    }
}