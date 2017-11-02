<?php

namespace App\Controller;

use GraphQL\Error\Debug;
use GraphQL\Language\Parser;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GraphQL\Server\StandardServer;

class GraphQLController extends Controller
{
    public function index(Request $httpFoundationRequest)
    {
        $rootValue = [
            'sum' => function($root, $args, $context) {
                return $args['x'] + $args['y'];
            },
            'echo' => function($root, $args, $context) {
                return $args['message'];
            }
        ];

        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema(),
            'debug'  => Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE | Debug::RETHROW_INTERNAL_EXCEPTIONS,
            'rootValue' => $rootValue,
        ]);

        $psr7Factory = new DiactorosFactory();
        $request = $psr7Factory->createRequest($httpFoundationRequest);

        $result = $server->executePsrRequest($request);

        return new Response(json_encode($result));
    }

    private function getSchema()
    {
        $cacheDir = $this->container->getParameter('kernel.cache_dir');

        $schemaCache = $cacheDir . '/schema.cache';

        // TODO: only cache in prod mode
        if (!file_exists($schemaCache)) {
            $document = Parser::parse(file_get_contents(__DIR__ . '/../../config/schema.graphqls'));
            file_put_contents($schemaCache, "<?php\nreturn " . var_export(AST::toArray($document), true) . ';');
        } else {
            $document = AST::fromArray(require $schemaCache);
        }

        $typeConfigDecorator = function() {
            return [];
        };

        return BuildSchema::build($document, $typeConfigDecorator);
    }
}