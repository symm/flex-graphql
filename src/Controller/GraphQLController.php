<?php

namespace App\Controller;

use GraphQL\Error\Debug;
use GraphQL\Language\Parser;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GraphQL\Server\StandardServer;
use Zend\Diactoros\Response;

class GraphQLController extends Controller
{
    public function index(ServerRequestInterface $request)
    {
        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema(),
            'debug'  => Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE | Debug::RETHROW_INTERNAL_EXCEPTIONS,
        ]);


        $result = $server->executePsrRequest($request);

        $response = new Response();
        $response->getBody()->write(json_encode($result));

        return $response;
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

        $typeConfigDecorator = function($typeConfig, $typeDefinitionNode) {
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
        };

        return BuildSchema::build($document, $typeConfigDecorator);
    }
}